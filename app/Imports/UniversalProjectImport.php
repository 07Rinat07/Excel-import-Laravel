<?php

namespace App\Imports;

use App\Models\ExcelTemplate;
use App\Models\ExcelTemplateColumn;
use App\Models\Project;
use App\Models\ProjectValue;
use App\Models\Task;
use App\Services\Import\ImportFailureRecorder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Events\AfterImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class UniversalProjectImport implements ToCollection, WithEvents, WithStartRow, WithBatchInserts, WithChunkReading
{
    private Task $task;

    private ImportFailureRecorder $failureRecorder;

    private ?ExcelTemplate $template = null;

    private array $columnMap = [];

    private array $missingRequired = [];

    private static array $headings = [];

    private static string $currentSheetName = 'Sheet1';

    private static int $currentSheetIndex = 0;

    private int $totalRows = 0;

    private int $importedRows = 0;

    private int $failedRows = 0;

    public function __construct(Task $task, ImportFailureRecorder $failureRecorder)
    {
        $this->task = $task;
        $this->failureRecorder = $failureRecorder;
    }

    public function collection(Collection $collection): void
    {
        $hasMapping = is_array($this->task->column_map) && $this->task->template_id;
        if (! self::$headings && ! $hasMapping) {
            return;
        }

        $template = $this->resolveTemplate();
        if ($this->missingRequired) {
            $failures = [];
            foreach ($this->missingRequired as $label) {
                $failures[] = [
                    'row' => 1,
                    'key' => $label,
                    'message' => 'Отсутствует обязательная колонка в файле.',
                ];
            }
            $this->failureRecorder->recordCustomFailures($failures, $this->task);

            return;
        }
        $now = now();
        $failures = [];

        foreach ($collection as $rowIndex => $row) {
            $rowNumber = $rowIndex + $this->startRow();
            $values = $this->mapRowValues($row);
            if ($this->isRowEmpty($values)) {
                continue;
            }

            $this->totalRows++;
            $valuesByKey = $this->mapRowValuesByKey($values);

            $rowFailures = $this->validateRow($values);
            if ($rowFailures) {
                foreach ($rowFailures as $failure) {
                    $failures[] = [
                        'row' => $rowNumber,
                        'key' => $failure['key'],
                        'message' => $failure['message'],
                    ];
                }

                $this->failedRows++;
                continue;
            }

            $createdDate = $this->resolveDate($valuesByKey, ['created_at_time'], $now);
            $contractedDate = $this->resolveDate($valuesByKey, ['contracted_at'], $now);

            $project = Project::create([
                'type_id' => $this->task->type_id,
                'task_id' => $this->task->id,
                'template_id' => $template->id,
                'row_index' => $rowNumber,
                'sheet_name' => self::$currentSheetName,
                'sheet_index' => self::$currentSheetIndex,
                'title' => $this->buildTitle($values, $rowNumber),
                'created_at_time' => $createdDate,
                'contracted_at' => $contractedDate,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $valueRows = [];
            foreach ($this->columnMap as $index => $column) {
                $valueRows[] = [
                    'project_id' => $project->id,
                    'template_column_id' => $column->id,
                    'value' => $values[$index] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if ($valueRows) {
                ProjectValue::insert($valueRows);
            }

            $this->importedRows++;
        }

        if ($failures) {
            $this->failureRecorder->recordCustomFailures($failures, $this->task);
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
            BeforeSheet::class => [self::class, 'beforeSheet'],
            AfterImport::class => function (): void {
                $this->finalizeImport();
            },
        ];
    }

    public static function beforeSheet(BeforeSheet $event): void
    {
        $worksheet = $event->getSheet()->getDelegate();
        $highestColumn = $worksheet->getHighestColumn();
        $headerRow = $worksheet->rangeToArray("A1:{$highestColumn}1", null, true, false)[0] ?? [];
        self::$headings = $headerRow;
        self::$currentSheetName = $worksheet->getTitle() ?: 'Sheet1';
        $parent = $worksheet->getParent();
        if ($parent) {
            self::$currentSheetIndex = (int) $parent->getIndex($worksheet);
        } else {
            self::$currentSheetIndex = 0;
        }
    }

    private function resolveTemplate(): ExcelTemplate
    {
        if ($this->template) {
            return $this->template;
        }

        if (is_array($this->task->column_map) && $this->task->template_id) {
            $template = ExcelTemplate::with('columns')->findOrFail($this->task->template_id);
            $columnsById = $template->columns->keyBy('id');

            $mappedIds = [];
            foreach ($this->task->column_map as $index => $columnId) {
                $columnId = (int) $columnId;
                $column = $columnsById->get($columnId);
                if (! $column) {
                    continue;
                }
                $this->columnMap[(int) $index] = $column;
                $mappedIds[] = $columnId;
            }

            $this->missingRequired = $template->columns()
                ->where('is_required', true)
                ->whereNotIn('id', $mappedIds)
                ->pluck('label')
                ->all();

            $this->template = $template;

            return $template;
        }

        $headers = $this->normalizeHeadings();
        $headerKeys = array_column($headers, 'key');
        $hash = sha1(implode('|', array_column($headers, 'key')));

        $template = ExcelTemplate::query()
            ->where('type_id', $this->task->type_id)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            $template = ExcelTemplate::create([
                'type_id' => $this->task->type_id,
                'name' => 'Auto template '.now()->format('Y-m-d H:i'),
                'header_hash' => $hash,
                'is_active' => true,
                'created_by' => $this->task->user_id,
            ]);
        }

        $existingColumns = $template->columns()->get()->keyBy('key');
        foreach ($headers as $header) {
            if ($existingColumns->has($header['key'])) {
                $this->columnMap[$header['index']] = $existingColumns->get($header['key']);

                continue;
            }

            $column = ExcelTemplateColumn::create([
                'template_id' => $template->id,
                'key' => $header['key'],
                'label' => $header['label'],
                'data_type' => 'string',
                'is_required' => false,
                'position' => $header['position'],
            ]);

            $this->columnMap[$header['index']] = $column;
        }

        $template->update(['header_hash' => $hash]);
        $this->task->update(['template_id' => $template->id]);
        $this->template = $template;
        $this->missingRequired = $template->columns()
            ->where('is_required', true)
            ->whereNotIn('key', $headerKeys)
            ->pluck('label')
            ->all();

        return $template;
    }

    private function normalizeHeadings(): array
    {
        $headers = [];
        $usedKeys = [];
        $position = 0;

        foreach (self::$headings as $index => $label) {
            $label = trim((string) $label);
            if ($label === '') {
                continue;
            }

            $key = $this->normalizeKey($label);
            $key = $this->dedupeKey($key, $usedKeys);
            $usedKeys[] = $key;

            $headers[] = [
                'index' => $index,
                'label' => $label,
                'key' => $key,
                'position' => $position++,
            ];
        }

        return $headers;
    }

    private function normalizeKey(string $label): string
    {
        $key = Str::slug($label, '_');

        return $key !== '' ? $key : 'column';
    }

    private function dedupeKey(string $key, array $usedKeys): string
    {
        if (! in_array($key, $usedKeys, true)) {
            return $key;
        }

        $suffix = 2;
        $candidate = $key.'_'.$suffix;
        while (in_array($candidate, $usedKeys, true)) {
            $suffix++;
            $candidate = $key.'_'.$suffix;
        }

        return $candidate;
    }

    private function mapRowValues(Collection $row): array
    {
        $values = [];
        foreach ($this->columnMap as $index => $column) {
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
        $valuesByKey = [];
        foreach ($this->columnMap as $index => $column) {
            $valuesByKey[$column->key] = $values[$index] ?? null;
        }

        return $valuesByKey;
    }

    private function validateRow(array $values): array
    {
        $errors = [];

        foreach ($this->columnMap as $index => $column) {
            $value = $values[$index] ?? null;
            $label = $column->label ?: $column->key;

            if ($column->is_required && ($value === null || $value === '')) {
                $errors[] = ['key' => $label, 'message' => 'Поле обязательно для заполнения.'];

                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            $error = $this->validateType($value, $column->data_type);
            if ($error) {
                $errors[] = ['key' => $label, 'message' => $error];
            }
        }

        return $errors;
    }

    private function validateType(mixed $value, string $type): ?string
    {
        $normalized = strtolower($type);

        return match ($normalized) {
            'number' => is_numeric($value) ? null : 'Ожидается число.',
            'integer' => filter_var($value, FILTER_VALIDATE_INT) !== false ? null : 'Ожидается целое число.',
            'date' => strtotime((string) $value) !== false ? null : 'Ожидается дата.',
            'boolean' => $this->isBoolean($value) ? null : 'Ожидается логическое значение.',
            default => null,
        };
    }

    private function isBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return true;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['1', '0', 'true', 'false', 'yes', 'no', 'да', 'нет'], true);
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
            } catch (\Throwable $e) {
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
        $this->task->refresh();
        $this->task->update([
            'total_rows' => $this->totalRows,
            'imported_rows' => $this->importedRows,
        ]);

        if ($this->missingRequired || $this->failedRows > 0) {
            $this->task->update(['status' => Task::STATUS_ERROR]);
        }
    }
}
