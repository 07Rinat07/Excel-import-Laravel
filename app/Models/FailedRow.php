<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedRow extends Model
{
    protected $fillable = [
        'key',
        'message',
        'row',
        'task_id',
    ];

    protected $table = 'failed_rows';
}
