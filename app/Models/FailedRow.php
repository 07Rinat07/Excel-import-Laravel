<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FailedRow extends Model
{
    protected $fillable = [
        'task_id',
        'row_number',
        'key',
        'message',
        'row',
        'data',
        'is_valid',
        'error_messages',
        'errors',
        'corrected_data',
        'is_corrected',
    ];

    protected $casts = [
        'row' => 'integer',
        'row_number' => 'integer',
        'data' => 'array',
        'error_messages' => 'array',
        'errors' => 'array',
        'corrected_data' => 'array',
        'is_valid' => 'boolean',
        'is_corrected' => 'boolean',
    ];

    protected $table = 'failed_rows';

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * Mark row as corrected with new data
     */
    public function markCorrected(array $correctedData): void
    {
        $this->update([
            'corrected_data' => $correctedData,
            'is_corrected' => true,
        ]);
    }

    /**
     * Get the original row data
     */
    public function getOriginalData(): array
    {
        return $this->row ?? [];
    }

    /**
     * Get corrected or original data
     */
    public function getDataForReimport(): array
    {
        return $this->is_corrected ? ($this->corrected_data ?? []) : ($this->row ?? []);
    }
}
