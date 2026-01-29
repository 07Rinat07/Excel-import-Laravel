<?php

declare(strict_types=1);

namespace App\Application\Import\DTO;

/**
 * DTO: StartImportResponse
 *
 * Output data transfer object for starting an import.
 */
final class StartImportResponse
{
    public function __construct(
        public readonly int $importTaskId,
        public readonly string $status,
        public readonly string $fileName,
        public readonly int $createdAt,
    ) {}
}
