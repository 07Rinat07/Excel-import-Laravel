<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProjectValuesMultiSheetExport implements WithMultipleSheets
{
    /**
     * @var array<int, array{title:string, headings:array<int,string>, rows:Collection}>
     */
    private array $sheets;

    /**
     * @param  array<int, array{title:string, headings:array<int,string>, rows:Collection}>  $sheets
     */
    public function __construct(array $sheets)
    {
        $this->sheets = $sheets;
    }

    public function sheets(): array
    {
        return array_map(function (array $sheet) {
            return new ProjectValuesSheetExport(
                $sheet['headings'],
                $sheet['rows'],
                $sheet['title']
            );
        }, $this->sheets);
    }
}
