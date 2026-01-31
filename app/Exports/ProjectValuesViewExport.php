<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectValuesViewExport implements FromView, WithStyles, WithColumnWidths
{
    private string $view;
    private array $data;
    private array $columnWidths;

    public function __construct(string $view, array $data, array $columnWidths = [])
    {
        $this->view = $view;
        $this->data = $data;
        $this->columnWidths = $columnWidths;
    }

    public function view(): View
    {
        return view($this->view, $this->data);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return $this->columnWidths;
    }
}
