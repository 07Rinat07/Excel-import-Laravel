<?php

declare(strict_types=1);

namespace App\Application\Import\DTO;

/**
 * DTO: StartImportRequest
 *
 * Input data transfer object for starting an import.
 */
final class StartImportRequest
{
    public function __construct(
        public readonly int $userId,
        public readonly int $templateId,
        public readonly string $filePath,
        public readonly string $fileName,
        public readonly string $mappingStrategy = 'manual',
    ) {}
}
