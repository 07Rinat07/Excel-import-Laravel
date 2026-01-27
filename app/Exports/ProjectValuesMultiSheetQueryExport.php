<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProjectValuesMultiSheetQueryExport implements WithMultipleSheets
{
    /**
     * @var array<int, ProjectValuesQueryExport>
     */
    private array $sheets;

    /**
     * @param  array<int, ProjectValuesQueryExport>  $sheets
     */
    public function __construct(array $sheets)
    {
        $this->sheets = $sheets;
    }

    public function sheets(): array
    {
        return $this->sheets;
    }
}
