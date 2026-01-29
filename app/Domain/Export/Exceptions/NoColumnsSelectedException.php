<?php

declare(strict_types=1);

namespace App\Domain\Export\Exceptions;

/**
 * Exception thrown when no columns are selected for export.
 */
class NoColumnsSelectedException extends ExportException
{
    public function __construct()
    {
        parent::__construct('At least one column must be selected for export');
    }
}
