<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProjectValuesSheetExport extends ProjectValuesExport implements WithTitle
{
    private string $title;

    /**
     * @param  array<int, string>  $headings
     * @param  Collection<int, array<int, mixed>>  $rows
     */
    public function __construct(array $headings, Collection $rows, string $title)
    {
        parent::__construct($headings, $rows);
        $this->title = $title;
    }

    public function title(): string
    {
        return $this->title;
    }
}
