<?php

declare(strict_types=1);

namespace App\Domain\Import\Exceptions;

/**
 * Exception thrown when validation fails.
 */
class ValidationFailedException extends ImportException
{
    public function __construct(
        string $message = 'Validation failed',
        public readonly array $errors = [],
    ) {
        parent::__construct($message);
    }
}
