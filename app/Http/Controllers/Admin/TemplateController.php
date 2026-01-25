<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TemplateUpdateRequest;
use App\Models\ExcelTemplate;
use App\Models\ExcelTemplateColumn;
use App\Models\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TemplateController extends Controller
{
    public function index(): Response
    {
        $templates = ExcelTemplate::query()
            ->with('type')
            ->withCount('columns')
            ->latest('id')
            ->paginate(10)
            ->through(function (ExcelTemplate $template) {
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                    'type' => $template->type ? [
                        'id' => $template->type->id,
                        'title' => $template->type->title,
                    ] : null,
                    'columns_count' => $template->columns_count,
                    'is_active' => $template->is_active,
                    'updated_at' => $template->updated_at?->format('Y-m-d H:i'),
                ];
            });

        return Inertia::render('Admin/Templates/Index', [
            'templates' => $templates,
        ]);
    }

    public function edit(ExcelTemplate $template): Response
    {
        $template->load('columns');

        return Inertia::render('Admin/Templates/Edit', [
            'template' => [
                'id' => $template->id,
                'name' => $template->name,
                'type_id' => $template->type_id,
                'is_active' => $template->is_active,
                'columns' => $template->columns->sortBy('position')->values()->map(function (ExcelTemplateColumn $column) {
                    return [
                        'id' => $column->id,
                        'label' => $column->label,
                        'key' => $column->key,
                        'data_type' => $column->data_type,
                        'is_required' => $column->is_required,
                        'position' => $column->position,
                    ];
                }),
            ],
            'types' => Type::query()->orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function update(TemplateUpdateRequest $request, ExcelTemplate $template)
    {
        $data = $request->validated();

        $template->update([
            'name' => $data['name'],
            'type_id' => $data['type_id'] ?? null,
            'is_active' => $data['is_active'] ?? false,
        ]);

        $columns = $data['columns'] ?? [];
        $deletedIds = $data['deleted_ids'] ?? [];

        $normalized = [];
        foreach ($columns as $index => $column) {
            $keySource = $column['key'] ?? $column['label'];
            $key = Str::slug($keySource, '_');
            if ($key === '') {
                $key = 'column_'.($index + 1);
            }
            $normalized[] = array_merge($column, [
                'key' => $key,
                'position' => $column['position'] ?? $index,
            ]);
        }

        $duplicates = collect($normalized)
            ->groupBy('key')
            ->filter(fn ($group) => $group->count() > 1);

        if ($duplicates->isNotEmpty()) {
            throw ValidationException::withMessages([
                'columns' => 'Ключи колонок должны быть уникальными.',
            ]);
        }

        foreach ($normalized as $column) {
            if (! empty($column['id'])) {
                ExcelTemplateColumn::where('template_id', $template->id)
                    ->where('id', $column['id'])
                    ->update([
                        'label' => $column['label'],
                        'key' => $column['key'],
                        'data_type' => $column['data_type'],
                        'is_required' => (bool) ($column['is_required'] ?? false),
                        'position' => $column['position'],
                    ]);

                continue;
            }

            ExcelTemplateColumn::create([
                'template_id' => $template->id,
                'label' => $column['label'],
                'key' => $column['key'],
                'data_type' => $column['data_type'],
                'is_required' => (bool) ($column['is_required'] ?? false),
                'position' => $column['position'],
            ]);
        }

        if ($deletedIds) {
            ExcelTemplateColumn::where('template_id', $template->id)
                ->whereIn('id', $deletedIds)
                ->delete();
        }

        return redirect()->route('admin.templates.edit', $template->id)
            ->with('message', 'Template updated.');
    }

    public function importColumns(\Illuminate\Http\Request $request, ExcelTemplate $template)
    {
        $data = $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,tsv,txt|max:10240',
        ]);

        $path = $data['file']->getRealPath();
        $headers = $this->extractHeadersFromPath($path);

        if (! $headers) {
            return redirect()->back()->withErrors(['file' => 'Не удалось найти заголовки в первой строке.']);
        }

        DB::transaction(function () use ($template, $headers) {
            ExcelTemplateColumn::where('template_id', $template->id)->delete();

            $usedKeys = [];
            $position = 0;
            $keys = [];

            foreach ($headers as $label) {
                $key = Str::slug($label, '_');
                if ($key === '') {
                    $key = 'column_'.$position;
                }
                $key = $this->dedupeKey($key, $usedKeys);
                $usedKeys[] = $key;
                $keys[] = $key;

                ExcelTemplateColumn::create([
                    'template_id' => $template->id,
                    'label' => $label,
                    'key' => $key,
                    'data_type' => 'string',
                    'is_required' => false,
                    'position' => $position++,
                ]);
            }

            $template->update([
                'header_hash' => sha1(implode('|', $keys)),
            ]);
        });

        return redirect()->route('admin.templates.edit', $template->id)
            ->with('message', 'Columns imported from Excel.');
    }

    private function extractHeadersFromPath(string $path): array
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $highestColumn = $sheet->getHighestColumn();
        $row = $sheet->rangeToArray("A1:{$highestColumn}1", null, true, false)[0] ?? [];

        $headers = [];
        foreach ($row as $index => $label) {
            $label = trim((string) $label);
            if ($label === '') {
                continue;
            }
            $headers[] = $label;
        }

        return $headers;
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
}
