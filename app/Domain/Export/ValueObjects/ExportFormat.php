<?php

declare(strict_types=1);

namespace App\Domain\Export\ValueObjects;

use App\Domain\ValueObject;

/**
 * Value Object: Export format (XLSX, CSV, TSV, ODS).
 */
final class ExportFormat extends ValueObject
{
    public const XLSX = 'xlsx';
    public const CSV = 'csv';
    public const TSV = 'tsv';
    public const ODS = 'ods';

    private function __construct(
        public readonly string $value,
    ) {}

    public static function xlsx(): self
    {
        return new self(self::XLSX);
    }

    public static function csv(): self
    {
        return new self(self::CSV);
    }

    public static function tsv(): self
    {
        return new self(self::TSV);
    }

    public static function ods(): self
    {
        return new self(self::ODS);
    }

    public static function from(string $format): self
    {
        return match (strtolower($format)) {
            self::XLSX => self::xlsx(),
            self::CSV => self::csv(),
            self::TSV => self::tsv(),
            self::ODS => self::ods(),
            default => throw new \InvalidArgumentException("Invalid export format: {$format}"),
        };
    }

    public function extension(): string
    {
        return $this->value;
    }

    public function mimeType(): string
    {
        return match ($this->value) {
            self::XLSX => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            self::CSV => 'text/csv',
            self::TSV => 'text/tab-separated-values',
            self::ODS => 'application/vnd.oasis.opendocument.spreadsheet',
        };
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function toString(): string
    {
        return strtoupper($this->value);
    }
}
