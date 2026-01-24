<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportLog extends Model
{
    protected $fillable = [
        'user_id',
        'source_type',
        'source_id',
        'format',
        'status',
        'file_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
