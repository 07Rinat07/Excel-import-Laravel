<?php

declare(strict_types=1);

namespace App\Infrastructure\Import;

use App\Domain\Import\Services\RowProcessorService;
use App\Domain\Import\ValueObjects\Row;
use App\Domain\Import\ValueObjects\ColumnMapping;

/**
 * Service: RowProcessorService
 *
 * Implementation of RowProcessorService.
 * Transforms and processes rows before storage.
 */
final class PhpSpreadsheetRowProcessorService implements RowProcessorService
{
    /**
     * Process a single row with column mappings.
     *
     * @param array<ColumnMapping> $mappings
     * @return array<string, mixed>
     */
    public function process(Row $row, array $mappings): array
    {
        $processed = [];

        foreach ($mappings as $mapping) {
            $sourceValue = $row->get($mapping->source()->name());

            // Apply transformations based on target field
            $processedValue = $this->transformValue(
                $sourceValue,
                $mapping->targetField()
            );

            $processed[$mapping->targetField()] = $processedValue;
        }

        return $processed;
    }

    /**
     * Process multiple rows in batch.
     *
     * @param array<Row> $rows
     * @param array<ColumnMapping> $mappings
     * @return array<array<string, mixed>>
     */
    public function processBatch(array $rows, array $mappings): array
    {
        return array_map(
            fn(Row $row) => $this->process($row, $mappings),
            $rows
        );
    }

    /**
     * Transform value based on target field name.
     *
     * Apply field-specific transformations:
     * - Trim whitespace
     * - Convert types
     * - Normalize dates
     * - etc.
     */
    private function transformValue(mixed $value, string $targetField): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Convert to string and trim
        $stringValue = trim((string) $value);

        // Field-specific transformations
        return match (true) {
            // Date fields
            str_contains($targetField, 'date') || str_contains($targetField, 'at') =>
                $this->transformDate($stringValue),

            // Email fields
            str_contains($targetField, 'email') =>
                strtolower($stringValue),

            // Numeric fields
            str_contains($targetField, 'amount') ||
            str_contains($targetField, 'price') ||
            str_contains($targetField, 'quantity') =>
                $this->transformNumeric($stringValue),

            // Boolean fields
            str_contains($targetField, 'is_') ||
            str_contains($targetField, 'active') =>
                $this->transformBoolean($stringValue),

            // Default: return as string
            default => $stringValue,
        };
    }

    /**
     * Transform string to date.
     */
    private function transformDate(string $value): ?string
    {
        // Try multiple date formats
        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d',
            'd.m.Y',
            'd/m/Y',
            'm/d/Y',
            'Y/m/d',
        ];

        foreach ($formats as $format) {
            $parsed = \DateTime::createFromFormat($format, $value);
            if ($parsed !== false) {
                return $parsed->format('Y-m-d H:i:s');
            }
        }

        return $value; // Return as-is if no format matches
    }

    /**
     * Transform string to numeric.
     */
    private function transformNumeric(string $value): ?float
    {
        // Remove common currency symbols and spaces
        $cleaned = preg_replace('/[^\d.,\-]/', '', $value);

        if ($cleaned === null || $cleaned === '') {
            return null;
        }

        // Detect decimal separator (comma or period)
        $lastDot = strrpos($cleaned, '.');
        $lastComma = strrpos($cleaned, ',');

        if ($lastDot !== false && $lastComma !== false) {
            // Both exist - which one is decimal separator?
            if ($lastDot > $lastComma) {
                // Period is decimal separator
                $cleaned = str_replace(',', '', $cleaned);
            } else {
                // Comma is decimal separator
                $cleaned = str_replace('.', '', $cleaned);
                $cleaned = str_replace(',', '.', $cleaned);
            }
        } elseif ($lastComma !== false) {
            // Only comma - assume European format
            $cleaned = str_replace(',', '.', $cleaned);
        }

        $numeric = (float) $cleaned;
        return $numeric !== 0.0 ? $numeric : null;
    }

    /**
     * Transform string to boolean.
     */
    private function transformBoolean(string $value): bool
    {
        $truthy = ['1', 'true', 'yes', 'y', 'on', 'enabled', 'active', 'да'];
        $falsy = ['0', 'false', 'no', 'n', 'off', 'disabled', 'inactive', 'нет'];

        $lower = strtolower($value);

        if (in_array($lower, $truthy, true)) {
            return true;
        }
        if (in_array($lower, $falsy, true)) {
            return false;
        }

        // Default: truthy if not empty
        return !empty($value);
    }
}
