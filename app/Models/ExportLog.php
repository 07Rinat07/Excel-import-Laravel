<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportLog extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'source_type',
        'source_id',
        'format',
        'status',
        'file_name',
        'columns_count',
        'rows_processed',
        'failure_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function failedRows()
    {
        return $this->hasMany(FailedRow::class, 'task_id');
    }
}
