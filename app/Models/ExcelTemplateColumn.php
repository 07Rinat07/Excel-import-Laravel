<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelTemplateColumn extends Model
{
    use HasFactory;
    protected $fillable = [
        'template_id',
        'key',
        'label',
        'data_type',
        'is_required',
        'validation_rules',
        'position',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'validation_rules' => 'array',
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
