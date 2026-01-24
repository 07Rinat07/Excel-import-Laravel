<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectValue extends Model
{
    protected $fillable = [
        'project_id',
        'template_column_id',
        'value',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function column()
    {
        return $this->belongsTo(ExcelTemplateColumn::class, 'template_column_id');
    }
}
