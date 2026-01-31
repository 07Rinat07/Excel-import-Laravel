<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportPreset extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'source_type',
        'source_id',
        'format',
        'columns',
        'labels',
        'sheet_name',
        'sheet_index',
        'filter_user_id',
    ];

    protected $casts = [
        'columns' => 'array',
        'labels' => 'array',
        'sheet_index' => 'integer',
        'filter_user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
