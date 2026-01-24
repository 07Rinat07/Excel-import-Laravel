<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcelTemplateColumn extends Model
{
    protected $fillable = [
        'template_id',
        'key',
        'label',
        'data_type',
        'is_required',
        'position',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'position' => 'integer',
    ];

    public function template()
    {
        return $this->belongsTo(ExcelTemplate::class, 'template_id');
    }

    public function values()
    {
        return $this->hasMany(ProjectValue::class, 'template_column_id');
    }
}
