<?php

namespace App\Services\Export;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

class EnhancedExportService
{
    /**
     * Style header row
     */
    public function styleHeader(Spreadsheet $spreadsheet, int $row = 1): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $highestColumn = $sheet->getHighestColumn();

        for ($col = 'A'; $col !== chr(ord($highestColumn) + 1); $col++) {
            $cell = $sheet->getCell($col . $row);

            // Background color
            $cell->getStyle()->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('FF4472C4');

            // Text color
            $cell->getStyle()->getFont()
                ->setColor(new Color('FFFFFFFF'))
                ->setBold(true)
                ->setSize(12);

            // Alignment
            $cell->getStyle()->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);

            // Border
            $cell->getStyle()->getBorders()
                ->getBottom()
                ->setBorderStyle(Border::BORDER_THICK)
                ->setColor(new Color('FF000000'));
        }

        // Set header row height
        $sheet->getRowDimension($row)->setRowHeight(25);
    }

    /**
     * Auto-fit column widths
     */
    public function autoFitColumns(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $highestColumn = $sheet->getHighestColumn();

        for ($col = 'A'; $col !== chr(ord($highestColumn) + 1); $col++) {
            $maxLength = 0;

            for ($row = 1; $row <= $sheet->getHighestRow(); $row++) {
                $cell = $sheet->getCell($col . $row);
                $cellLength = strlen($cell->getValue());

                if ($cellLength > $maxLength) {
                    $maxLength = $cellLength;
                }
            }

            // Set width with padding
            $sheet->getColumnDimension($col)
                ->setWidth(min($maxLength + 2, 50));
        }
    }

    /**
     * Alternate row colors
     */
    public function alternateRowColors(Spreadsheet $spreadsheet, int $startRow = 2): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        for ($row = $startRow; $row <= $highestRow; $row++) {
            $fillColor = ($row % 2 === 0) ? 'FFF2F2F2' : 'FFFFFFFF';

            for ($col = 'A'; $col !== chr(ord($highestColumn) + 1); $col++) {
                $cell = $sheet->getCell($col . $row);
                $cell->getStyle()->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB($fillColor);

                // Add light borders
                $cell->getStyle()->getBorders()
                    ->getBottom()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->setColor(new Color('FFE0E0E0'));
            }
        }
    }

    /**
     * Add borders to data range
     */
    public function addBorders(Spreadsheet $spreadsheet, string $range): void
    {
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getStyle($range)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new Color('FF000000'));
    }

    /**
     * Format numbers in a range
     */
    public function formatNumbers(Spreadsheet $spreadsheet, string $range, string $format = '0.00'): void
    {
        $spreadsheet->getActiveSheet()
            ->getStyle($range)
            ->getNumberFormat()
            ->setFormatCode($format);
    }

    /**
     * Format currency
     */
    public function formatCurrency(Spreadsheet $spreadsheet, string $range, string $currencyCode = 'USD'): void
    {
        $format = match ($currencyCode) {
            'EUR' => '[$€-407] #,##0.00',
            'GBP' => '[$£-809] #,##0.00',
            'JPY' => '[$¥-411] #,##0',
            default => '[$$ -409] #,##0.00',
        };

        $spreadsheet->getActiveSheet()
            ->getStyle($range)
            ->getNumberFormat()
            ->setFormatCode($format);
    }

    /**
     * Freeze panes (header row)
     */
    public function freezeHeaderRow(Spreadsheet $spreadsheet): void
    {
        $spreadsheet->getActiveSheet()
            ->freezePane('A2');
    }

    /**
     * Add data validation
     */
    public function addDataValidation(
        Spreadsheet $spreadsheet,
        string $range,
        array $allowedValues
    ): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $validation = $sheet->getDataValidation($range);
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setFormula1('"' . implode(',', $allowedValues) . '"');
        $validation->setShowDropDown(true);
    }

    /**
     * Add summary statistics sheet
     */
    public function addSummarySheet(
        Spreadsheet $spreadsheet,
        array $statistics
    ): void
    {
        $summary = $spreadsheet->createSheet();
        $summary->setTitle('Summary');

        $row = 1;
        $summary->getCell('A' . $row)->setValue('Statistics');
        $summary->getCell('A' . $row)->getStyle()->getFont()->setBold(true)->setSize(14);

        $row += 2;
        foreach ($statistics as $label => $value) {
            $summary->getCell('A' . $row)->setValue($label);
            $summary->getCell('B' . $row)->setValue($value);
            $row++;
        }

        $summary->getColumnDimension('A')->setWidth(30);
        $summary->getColumnDimension('B')->setWidth(20);
    }

    /**
     * Create a bar chart from data range
     */
    public function addBarChart(
        Spreadsheet $spreadsheet,
        string $labelRange,
        string $valueRange,
        string $title = 'Chart',
        string $topLeft = 'D2',
        string $bottomRight = 'M20'
    ): void
    {
        $labels = [new DataSeriesValues('String', $labelRange, null, 1)];
        $values = [new DataSeriesValues('Number', $valueRange, null, 1)];
        $categories = [new DataSeriesValues('String', $labelRange, null, 1)];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($values) - 1),
            $labels,
            $categories,
            $values
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $chart = new Chart($title, new Title($title), $legend, $plotArea, true, 0, null, null);
        $chart->setTopLeftPosition($topLeft);
        $chart->setBottomRightPosition($bottomRight);

        $spreadsheet->getActiveSheet()->addChart($chart);
    }

    /**
     * Create a line chart from data range
     */
    public function addLineChart(
        Spreadsheet $spreadsheet,
        string $labelRange,
        string $valueRange,
        string $title = 'Chart',
        string $topLeft = 'D2',
        string $bottomRight = 'M20'
    ): void
    {
        $labels = [new DataSeriesValues('String', $labelRange, null, 1)];
        $values = [new DataSeriesValues('Number', $valueRange, null, 1)];
        $categories = [new DataSeriesValues('String', $labelRange, null, 1)];

        $series = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($values) - 1),
            $labels,
            $categories,
            $values
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $chart = new Chart($title, new Title($title), $legend, $plotArea, true, 0, null, null);
        $chart->setTopLeftPosition($topLeft);
        $chart->setBottomRightPosition($bottomRight);

        $spreadsheet->getActiveSheet()->addChart($chart);
    }

    /**
     * Add image to sheet
     */
    public function addImage(
        Spreadsheet $spreadsheet,
        string $imagePath,
        string $cell = 'A1'
    ): void
    {
        if (!file_exists($imagePath)) {
            return;
        }

        $drawing = new Drawing();
        $drawing->setPath($imagePath);
        $drawing->setCoordinates($cell);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());
    }
}
