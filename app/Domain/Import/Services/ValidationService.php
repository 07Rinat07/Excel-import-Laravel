<?php

declare(strict_types=1);

namespace App\Domain\Import\Services;

use App\Domain\Import\ValueObjects\Row;
use App\Domain\Import\ValueObjects\ValidationRule;
use App\Domain\Import\ValueObjects\ValidationResult;

/**
 * Service Interface: ValidationService
 *
 * Responsible for validating rows against defined rules.
 */
interface ValidationService
{
    /**
     * Validate a single row against validation rules.
     *
     * @param array<string, ValidationRule> $rules Rules mapped by field name
     */
    public function validate(Row $row, array $rules): ValidationResult;

    /**
     * Validate multiple rows in batch.
     *
     * @param array<Row> $rows
     * @param array<string, ValidationRule> $rules
     * @return array<ValidationResult>
     */
    public function validateBatch(array $rows, array $rules): array;
}
