<?php

declare(strict_types=1);

namespace App\Domain\Import\Services;

use App\Domain\Import\ValueObjects\FileContent;
use App\Domain\Import\ValueObjects\FileName;
use App\Domain\Import\ValueObjects\Row;

/**
 * Service Interface: ExcelParserService
 *
 * Responsible for parsing Excel files and extracting rows and columns.
 * Implementation-agnostic (interface only).
 */
interface ExcelParserService
{
    /**
     * Parse Excel file and extract headers and data rows.
     *
     * @return array{headers: array<string>, rows: array<Row>}
     */
    public function parse(FileName $fileName, FileContent $content): array;

    /**
     * Get supported file extensions.
     *
     * @return array<string>
     */
    public function supportedExtensions(): array;
}
