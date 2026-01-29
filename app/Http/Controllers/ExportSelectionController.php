<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Type;
use App\Models\ExcelTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class ExportSelectionController extends Controller
{
    /**
     * Show export selection UI for project
     */
    public function projectSelection(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $template = $project->template;
        $columns = $template?->columns()->orderBy('position')->get() ?? [];

        return Inertia::render('Export/ProjectSelection', [
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'columns' => $columns->map(fn ($col) => [
                'id' => $col->id,
                'key' => $col->key,
                'label' => $col->label,
                'include' => true,
            ])->values()->all(),
        ]);
    }

    /**
     * Store export selection and download file for project
     */
    public function projectExport(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $validated = $request->validate([
            'columns' => 'required|array|min:1',
            'columns.*' => 'required|string',
            'format' => 'required|in:xlsx,csv,tsv',
        ]);

        $template = $project->template;
        if (!$template) {
            return redirect()->back()->withErrors(['error' => 'Template not found']);
        }

        $selectedColumnIds = $validated['columns'];
        $selectedColumns = $template->columns()
            ->whereIn('id', $selectedColumnIds)
            ->get();

        if ($selectedColumns->isEmpty()) {
            return redirect()->back()->withErrors(['error' => 'No columns selected']);
        }

        $data = $this->prepareExportData($project, $selectedColumns);

        return $this->downloadExcel(
            $data,
            $selectedColumns,
            "project-{$project->id}",
            $validated['format']
        );
    }

    /**
     * Show export selection UI for task
     */
    public function taskSelection(Request $request, Task $task)
    {
        $this->authorize('view', $task);

        $template = $task->template;
        $columns = $template?->columns()->orderBy('position')->get() ?? [];

        return Inertia::render('Export/TaskSelection', [
            'task' => [
                'id' => $task->id,
                'status' => $task->status,
            ],
            'columns' => $columns->map(fn ($col) => [
                'id' => $col->id,
                'key' => $col->key,
                'label' => $col->label,
                'include' => true,
            ])->values()->all(),
        ]);
    }

    /**
     * Store export selection and download file for task
     */
    public function taskExport(Request $request, Task $task)
    {
        $this->authorize('view', $task);

        $validated = $request->validate([
            'columns' => 'required|array|min:1',
            'columns.*' => 'required|string',
            'format' => 'required|in:xlsx,csv,tsv',
        ]);

        $template = $task->template;
        if (!$template) {
            return redirect()->back()->withErrors(['error' => 'Template not found']);
        }

        $selectedColumnIds = $validated['columns'];
        $selectedColumns = $template->columns()
            ->whereIn('id', $selectedColumnIds)
            ->get();

        if ($selectedColumns->isEmpty()) {
            return redirect()->back()->withErrors(['error' => 'No columns selected']);
        }

        $data = $this->prepareTaskExportData($task, $selectedColumns);

        return $this->downloadExcel(
            $data,
            $selectedColumns,
            "task-{$task->id}",
            $validated['format']
        );
    }

    /**
     * Show export selection UI for type
     */
    public function typeSelection(Request $request, Type $type)
    {
        $this->authorize('export', $type);

        $template = $type->template;
        $columns = $template?->columns()->orderBy('position')->get() ?? [];

        return Inertia::render('Export/TypeSelection', [
            'type' => [
                'id' => $type->id,
                'title' => $type->title,
            ],
            'columns' => $columns->map(fn ($col) => [
                'id' => $col->id,
                'key' => $col->key,
                'label' => $col->label,
                'include' => true,
            ])->values()->all(),
        ]);
    }

    /**
     * Store export selection and download file for type
     */
    public function typeExport(Request $request, Type $type)
    {
        $this->authorize('export', $type);

        $validated = $request->validate([
            'columns' => 'required|array|min:1',
            'columns.*' => 'required|string',
            'format' => 'required|in:xlsx,csv,tsv',
        ]);

        $template = $type->template;
        if (!$template) {
            return redirect()->back()->withErrors(['error' => 'Template not found']);
        }

        $selectedColumnIds = $validated['columns'];
        $selectedColumns = $template->columns()
            ->whereIn('id', $selectedColumnIds)
            ->get();

        if ($selectedColumns->isEmpty()) {
            return redirect()->back()->withErrors(['error' => 'No columns selected']);
        }

        $data = $this->prepareTypeExportData($type, $selectedColumns, $request->user());

        return $this->downloadExcel(
            $data,
            $selectedColumns,
            "type-{$type->id}",
            $validated['format']
        );
    }

    /**
     * Prepare project export data with selected columns
     */
    private function prepareExportData(Project $project, $selectedColumns)
    {
        $columnKeys = $selectedColumns->pluck('key')->toArray();
        $data = [];

        // Add header row
        $headerRow = [];
        foreach ($selectedColumns as $column) {
            $headerRow[] = $column->label;
        }
        $data[] = $headerRow;

        // Add data rows
        $projects = Project::where('template_id', $project->template_id)->get();

        foreach ($projects as $proj) {
            $row = [];
            foreach ($columnKeys as $key) {
                $row[] = $proj->{$key} ?? '';
            }
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Prepare task export data with selected columns
     */
    private function prepareTaskExportData(Task $task, $selectedColumns)
    {
        $columnKeys = $selectedColumns->pluck('key')->toArray();
        $data = [];

        // Add header row
        $headerRow = [];
        foreach ($selectedColumns as $column) {
            $headerRow[] = $column->label;
        }
        $data[] = $headerRow;

        return $data;
    }

    /**
     * Prepare type export data with selected columns
     */
    private function prepareTypeExportData(Type $type, $selectedColumns, $user)
    {
        $columnKeys = $selectedColumns->pluck('key')->toArray();
        $data = [];

        // Add header row
        $headerRow = [];
        foreach ($selectedColumns as $column) {
            $headerRow[] = $column->label;
        }
        $data[] = $headerRow;

        // Add data rows for all projects of this type
        $projects = Project::where('type_id', $type->id)
            ->where('user_id', $user->id)
            ->get();

        foreach ($projects as $proj) {
            $row = [];
            foreach ($columnKeys as $key) {
                $row[] = $proj->{$key} ?? '';
            }
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Generate and download Excel file
     */
    private function downloadExcel($data, $columns, $filename, $format)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add data
        foreach ($data as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 1, $value);
            }
        }

        // Style header row
        $sheet->getStyle('1:1')->getFont()->setBold(true);
        $sheet->getStyle('1:1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFE0E0E0');

        // Auto-fit columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Save to temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $extension = 'xlsx';

        switch (strtolower($format)) {
            case 'csv':
                $writer = new Csv($spreadsheet);
                $extension = 'csv';
                break;
            case 'tsv':
                $writer = new Csv($spreadsheet);
                $writer->setDelimiter("\t");
                $extension = 'tsv';
                break;
            case 'xlsx':
            default:
                $writer = new Xlsx($spreadsheet);
                break;
        }

        $writer->save($tempFile);

        return response()->download($tempFile, "{$filename}.{$extension}", [
            'Content-Type' => $this->getContentType($extension),
            'Content-Disposition' => "attachment; filename=\"{$filename}.{$extension}\"",
        ])->deleteFileAfterSend(true);
    }

    /**
     * Get content type for file format
     */
    private function getContentType($extension)
    {
        $types = [
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv',
            'tsv' => 'text/tab-separated-values',
        ];

        return $types[$extension] ?? 'application/octet-stream';
    }
}
