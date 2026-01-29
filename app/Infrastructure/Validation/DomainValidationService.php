<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation;

use App\Domain\Import\Services\ValidationService;
use App\Domain\Import\ValueObjects\Row;
use App\Domain\Import\ValueObjects\ValidationRule;
use App\Domain\Import\ValueObjects\ValidationResult;

/**
 * Service: DomainValidationService
 *
 * Implementation of ValidationService.
 * Validates rows against domain validation rules.
 */
final class DomainValidationService implements ValidationService
{
    /**
     * Validate a single row against validation rules.
     *
     * @param array<string, ValidationRule> $rules Rules mapped by field name
     */
    public function validate(Row $row, array $rules): ValidationResult
    {
        $errors = [];

        foreach ($rules as $fieldName => $rule) {
            $value = $row->get($fieldName);
            $fieldErrors = $this->validateField($fieldName, $value, $rule);

            if (!empty($fieldErrors)) {
                $errors[$fieldName] = $fieldErrors;
            }
        }

        return empty($errors)
            ? ValidationResult::success()
            : ValidationResult::failed($errors);
    }

    /**
     * Validate multiple rows in batch.
     *
     * @param array<Row> $rows
     * @param array<string, ValidationRule> $rules
     * @return array<ValidationResult>
     */
    public function validateBatch(array $rows, array $rules): array
    {
        return array_map(
            fn(Row $row) => $this->validate($row, $rules),
            $rows
        );
    }

    /**
     * Validate a single field against a rule.
     *
     * @return array<string> List of error messages
     */
    private function validateField(string $fieldName, mixed $value, ValidationRule $rule): array
    {
        $errors = [];

        match ($rule->type()) {
            ValidationRule::REQUIRED => $errors = $this->validateRequired($value),
            ValidationRule::NUMERIC => $errors = $this->validateNumeric($value),
            ValidationRule::STRING => $errors = $this->validateString($value),
            ValidationRule::EMAIL => $errors = $this->validateEmail($value),
            ValidationRule::MAX_LENGTH => $errors = $this->validateMaxLength($value, $rule->value()),
            ValidationRule::MIN_LENGTH => $errors = $this->validateMinLength($value, $rule->value()),
            ValidationRule::PATTERN => $errors = $this->validatePattern($value, $rule->value()),
            ValidationRule::ENUM => $errors = $this->validateEnum($value, $rule->value()),
            ValidationRule::UNIQUE => $errors = [], // Will be handled at persistence layer
            default => $errors = [],
        };

        return array_map(
            fn(string $msg) => "{$fieldName}: {$msg}",
            $errors
        );
    }

    /**
     * @return array<string>
     */
    private function validateRequired(mixed $value): array
    {
        if ($value === null || $value === '' || trim((string) $value) === '') {
            return ['This field is required'];
        }
        return [];
    }

    /**
     * @return array<string>
     */
    private function validateNumeric(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }
        if (!is_numeric($value)) {
            return ['This field must be numeric'];
        }
        return [];
    }

    /**
     * @return array<string>
     */
    private function validateString(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }
        if (!is_string($value)) {
            return ['This field must be a string'];
        }
        return [];
    }

    /**
     * @return array<string>
     */
    private function validateEmail(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return ['This field must be a valid email address'];
        }
        return [];
    }

    /**
     * @return array<string>
     */
    private function validateMaxLength(mixed $value, mixed $maxLength): array
    {
        if ($value === null || $value === '') {
            return [];
        }
        $length = strlen((string) $value);
        if ($length > (int) $maxLength) {
            return ["This field cannot exceed {$maxLength} characters (current: {$length})"];
        }
        return [];
    }

    /**
     * @return array<string>
     */
    private function validateMinLength(mixed $value, mixed $minLength): array
    {
        if ($value === null || $value === '') {
            return [];
        }
        $length = strlen((string) $value);
        if ($length < (int) $minLength) {
            return ["This field must have at least {$minLength} characters (current: {$length})"];
        }
        return [];
    }

    /**
     * @return array<string>
     */
    private function validatePattern(mixed $value, mixed $pattern): array
    {
        if ($value === null || $value === '') {
            return [];
        }
        if (!preg_match((string) $pattern, (string) $value)) {
            return ['This field format is invalid'];
        }
        return [];
    }

    /**
     * @return array<string>
     */
    private function validateEnum(mixed $value, mixed $allowedValues): array
    {
        if ($value === null || $value === '') {
            return [];
        }
        if (!is_array($allowedValues)) {
            return [];
        }
        if (!in_array($value, $allowedValues, true)) {
            $allowed = implode(', ', $allowedValues);
            return ["This field must be one of: {$allowed}"];
        }
        return [];
    }
}
