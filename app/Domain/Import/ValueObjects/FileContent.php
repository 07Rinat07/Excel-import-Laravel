<?php

declare(strict_types=1);

namespace App\Domain\Import\ValueObjects;

use App\Domain\ValueObject;

/**
 * Value Object: Import file content (binary data from uploaded file).
 */
final class FileContent extends ValueObject
{
    public function __construct(
        public readonly string $content,
    ) {
        if (empty($this->content)) {
            throw new \InvalidArgumentException('File content cannot be empty');
        }
    }

    public static function from(string $content): self
    {
        return new self($content);
    }

    public static function fromPath(string $filePath): self
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File not found: {$filePath}");
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new \InvalidArgumentException("Could not read file: {$filePath}");
        }

        return new self($content);
    }

    public function path(): string
    {
        // This is a calculated property - we don't store the path
        // It's mainly for compatibility with code that expects a path
        return '';
    }

    public function content(): string
    {
        return $this->content;
    }

    public function size(): int
    {
        return strlen($this->content);
    }

    public function hash(): string
    {
        return hash('sha256', $this->content);
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && hash_equals($this->content, $other->content);
    }

    public function toString(): string
    {
        return "FileContent({$this->size()} bytes, hash: {$this->hash()})";
    }
}
