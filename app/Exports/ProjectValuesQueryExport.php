<?php

namespace App\Exports;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProjectValuesQueryExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, WithTitle
{
    private Builder $query;

    private Collection $columns;

    /**
     * @var array<int, string>
     */
    private array $labels;

    private ?string $title;

    /**
     * @param  Collection<int, \App\Models\ExcelTemplateColumn>  $columns
     * @param  array<int, string>  $labels
     */
    public function __construct(Builder $query, Collection $columns, array $labels = [], ?string $title = null)
    {
        $this->query = $query;
        $this->columns = $columns;
        $this->labels = $labels;
        $this->title = $title;
    }

    public function query(): Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        return $this->columns->map(function ($column) {
            $custom = trim((string) ($this->labels[$column->id] ?? ''));
            return $custom !== '' ? $custom : $column->label;
        })->all();
    }

    /**
     * @param  Project  $project
     */
    public function map($project): array
    {
        $values = $project->values->keyBy('template_column_id');

        return $this->columns->map(function ($column) use ($values) {
            return optional($values->get($column->id))->value;
        })->all();
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function title(): string
    {
        return $this->title ?? 'Sheet1';
    }
}
