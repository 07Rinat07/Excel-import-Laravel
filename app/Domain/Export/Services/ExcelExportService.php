<?php

declare(strict_types=1);

namespace App\Domain\Export\Services;

use App\Domain\Export\ValueObjects\ExportFormat;
use App\Domain\Export\ValueObjects\ExportColumns;

/**
 * Service Interface: ExcelExportService
 *
 * Responsible for exporting data to Excel files in various formats.
 * Implementation-agnostic (interface only).
 */
interface ExcelExportService
{
    /**
     * Export data to Excel file.
     *
     * @param array<string> $headers Column headers
     * @param array<array<mixed>> $rows Data rows to export
     * @param ExportFormat $format Export format (xlsx, csv, tsv)
     * @return string File path of exported file
     */
    public function export(
        array $headers,
        array $rows,
        ExportFormat $format,
    ): string;

    /**
     * Get supported export formats.
     *
     * @return array<string>
     */
    public function supportedFormats(): array;
}
