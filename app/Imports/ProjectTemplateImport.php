<?php

namespace App\Imports;

use App\Models\ExcelTemplate;
use App\Models\Project;
use App\Models\ProjectValue;
use App\Models\Task;
use App\Services\Import\ImportFailureRecorder;
use App\Services\DataValidationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProjectTemplateImport implements ToCollection, WithChunkReading, WithBatchInserts, WithStartRow, WithEvents, ShouldQueue
{
    public $queue = 'imports';

    private int $taskId;
    private int $templateId;
    private int $typeId;
    private array $columnMap;
    private array $columns = [];
    private array $missingRequired = [];
    private bool $missingRequiredRecorded = false;
    private array $failures = [];
    private int $totalRows = 0;
    private int $importedRows = 0;
    private int $failedRows = 0;
    private string $currentSheetName = 'Sheet1';
    private int $currentSheetIndex = 0;
    private ImportFailureRecorder $failureRecorder;
    private DataValidationService $validationService;
    private ?Task $task = null;
    private array $validationColumns = [];
    private array $columnLabelsByKey = [];

    public function __construct(Task $task, ImportFailureRecorder $failureRecorder)
    {
        $this->taskId = $task->id;
        $this->templateId = $task->template_id;
        $this->typeId = $task->type_id;
        $this->columnMap = is_array($task->column_map) ? $task->column_map : [];
        $this->failureRecorder = $failureRecorder;
        $this->validationService = app(DataValidationService::class);
        $this->columns = $this->loadColumns();
    }

    public function collection(Collection $collection): void
    {
        if ($this->missingRequired && ! $this->missingRequiredRecorded) {
            $this->recordMissingRequired();
        }

        if ($this->missingRequiredRecorded || empty($this->columns)) {
            return;
        }

        foreach ($collection as $rowIndex => $row) {
            $rowNumber = $rowIndex + $this->startRow();
            $values = $this->normalizeRow($row);

            if ($this->isRowEmpty($values)) {
                continue;
            }

            $this->totalRows++;
            $valuesByKey = $this->mapRowValuesByKey($values);
            $rowFailures = $this->validateRow($values, $rowNumber);
            if ($rowFailures) {
                $this->failures = array_merge($this->failures, $rowFailures);
                $this->failedRows++;
                continue;
            }

            $project = $this->createProject($values, $valuesByKey, $rowNumber);
            $this->storeValues($project->id, $values);
            $this->importedRows++;
        }

        if ($this->failures) {
            $this->failureRecorder->recordCustomFailures($this->failures, $this->task());
            $this->failures = [];
        }
    }

    public function startRow(): int
    {
        return 2;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event): void {
                $worksheet = $event->getSheet()->getDelegate();
                $this->currentSheetName = $worksheet->getTitle() ?: 'Sheet1';
                $parent = $worksheet->getParent();
                $this->currentSheetIndex = $parent ? (int) $parent->getIndex($worksheet) : 0;
            },
            AfterImport::class => function (): void {
                $this->finalizeImport();
            },
        ];
    }

    private function loadColumns(): array
    {
        if (! $this->templateId || empty($this->columnMap)) {
            return [];
        }

        $template = ExcelTemplate::with('columns')->find($this->templateId);
        if (! $template) {
            return [];
        }

        $columnsById = $template->columns->keyBy('id');
        $mappedIds = [];
        $result = [];

        foreach ($this->columnMap as $index => $columnId) {
            $column = $columnsById->get((int) $columnId);
            if (! $column) {
                continue;
            }

            $mappedIds[] = $column->id;
            $result[(int) $index] = [
                'id' => $column->id,
                'key' => $column->key,
                'label' => $column->label,
                'data_type' => (string) $column->data_type,
                'is_required' => (bool) $column->is_required,
                'validation_rules' => $column->validation_rules ?? [],
            ];
        }

        $this->missingRequired = $template->columns
            ->filter(fn ($column) => $column->is_required && ! in_array($column->id, $mappedIds, true))
            ->pluck('label')
            ->all();

        $this->validationColumns = $this->buildValidationColumns($result);
        $this->columnLabelsByKey = $this->buildColumnLabelMap($result);

        return $result;
    }

    private function recordMissingRequired(): void
    {
        $failures = [];
        foreach ($this->missingRequired as $label) {
            $failures[] = [
                'row' => 1,
                'key' => $label,
                'message' => 'Отсутствует обязательная колонка в файле.',
            ];
        }

        if ($failures) {
            $this->failureRecorder->recordCustomFailures($failures, $this->task());
        }

        $this->failedRows += count($this->missingRequired);
        $this->missingRequiredRecorded = true;
    }

    private function normalizeRow(Collection|array $row): array
    {
        $values = $row instanceof Collection ? $row->toArray() : (array) $row;

        return array_map(fn ($value) => is_string($value) ? trim($value) : $value, $values);
    }

    private function mapRowValues(array $row): array
    {
        $values = [];
        foreach ($this->columns as $index => $column) {
            $value = $row[$index] ?? null;
            if (is_string($value)) {
                $value = trim($value);
            }
            $values[$index] = $value;
        }

        return $values;
    }

    private function mapRowValuesByKey(array $values): array
    {
        $mapped = [];
        foreach ($this->columns as $index => $column) {
            $mapped[$column['key']] = $values[$index] ?? null;
        }

        return $mapped;
    }

    private function validateRow(array $values, int $rowNumber): array
    {
        $valuesByKey = $this->mapRowValuesByKey($values);
        $validation = $this->validationService->validateRow($valuesByKey, $this->validationColumns);
        if ($validation['valid']) {
            return [];
        }

        $errors = [];
        foreach ($validation['errors'] as $key => $messages) {
            $label = $this->columnLabelsByKey[$key] ?? $key;
            foreach ((array) $messages as $message) {
                $errors[] = ['row' => $rowNumber, 'key' => $label, 'message' => $message];
            }
        }

        return $errors;
    }
    private function buildValidationColumns(array $columns): array
    {
        $result = [];
        foreach ($columns as $column) {
            $rules = is_array($column['validation_rules'] ?? null) ? $column['validation_rules'] : [];

            if (!empty($column['is_required']) && ! in_array('required', $rules, true)) {
                $rules[] = 'required';
            }

            $typeRule = $this->mapTypeToRule((string) ($column['data_type'] ?? ''));
            if ($typeRule) {
                $rules[] = $typeRule;
            }

            if (! in_array('required', $rules, true)) {
                $rules[] = 'nullable';
            }

            $result[] = [
                'key' => $column['key'],
                'label' => $column['label'],
                'validation_rules' => $rules,
            ];
        }

        return $result;
    }

    private function buildColumnLabelMap(array $columns): array
    {
        $labels = [];
        foreach ($columns as $column) {
            $labels[$column['key']] = $column['label'] ?: $column['key'];
        }

        return $labels;
    }

    private function mapTypeToRule(string $type): ?string
    {
        return match (strtolower($type)) {
            'number' => 'numeric',
            'integer' => 'integer',
            'date' => 'date',
            'boolean' => 'boolean',
            default => null,
        };
    }

    private function createProject(array $values, array $valuesByKey, int $rowNumber): Project
    {
        $now = now();
        $createdDate = $this->resolveDate($valuesByKey, ['created_at_time'], $now);
        $contractedDate = $this->resolveDate($valuesByKey, ['contracted_at'], $now);

        return Project::create([
            'type_id' => $this->typeId,
            'task_id' => $this->taskId,
            'template_id' => $this->templateId,
            'row_index' => $rowNumber,
            'sheet_name' => $this->currentSheetName,
            'sheet_index' => $this->currentSheetIndex,
            'title' => $this->buildTitle($values, $rowNumber),
            'created_at_time' => $createdDate,
            'contracted_at' => $contractedDate,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function storeValues(int $projectId, array $values): void
    {
        $now = now();
        $rows = [];

        foreach ($this->columns as $index => $column) {
            $rows[] = [
                'project_id' => $projectId,
                'template_column_id' => $column['id'],
                'value' => $values[$index] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows) {
            ProjectValue::insert($rows);
        }
    }

    private function resolveDate(array $valuesByKey, array $keys, \DateTimeInterface $fallback): string
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $valuesByKey)) {
                continue;
            }

            $normalized = $this->normalizeDate($valuesByKey[$key], $fallback);
            if ($normalized !== '') {
                return $normalized;
            }
        }

        return $fallback->format('Y-m-d');
    }

    private function normalizeDate(mixed $value, \DateTimeInterface $fallback): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_numeric($value)) {
            try {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable) {
                return $fallback->format('Y-m-d');
            }
        }

        $stringValue = trim((string) $value);
        if ($stringValue === '') {
            return $fallback->format('Y-m-d');
        }

        $timestamp = strtotime($stringValue);
        if ($timestamp === false) {
            return $fallback->format('Y-m-d');
        }

        return date('Y-m-d', $timestamp);
    }

    private function buildTitle(array $values, int $rowNumber): string
    {
        $firstValue = $values ? reset($values) : null;
        if (is_scalar($firstValue) && (string) $firstValue !== '') {
            return (string) $firstValue;
        }

        return 'Row '.$rowNumber;
    }

    private function isRowEmpty(array $values): bool
    {
        foreach ($values as $value) {
            if ($value === null) {
                continue;
            }
            if (is_string($value) && trim($value) === '') {
                continue;
            }
            return false;
        }

        return true;
    }

    private function finalizeImport(): void
    {
        $task = $this->task();
        $task->refresh();
        $task->update([
            'total_rows' => $this->totalRows,
            'imported_rows' => $this->importedRows,
        ]);

        if ($task->status !== Task::STATUS_ERROR) {
            if ($this->failedRows > 0) {
                $task->update(['status' => Task::STATUS_ERROR]);
            } else {
                $task->update(['status' => Task::STATUS_SUCCESS]);
            }
        }
    }

    private function task(): Task
    {
        if (! $this->task) {
            $this->task = Task::findOrFail($this->taskId);
        }

        return $this->task;
    }
}
