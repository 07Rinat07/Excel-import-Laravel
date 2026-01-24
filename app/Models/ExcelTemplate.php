<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcelTemplate extends Model
{
    protected $fillable = [
        'type_id',
        'name',
        'header_hash',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function columns()
    {
        return $this->hasMany(ExcelTemplateColumn::class, 'template_id')->orderBy('position');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'template_id');
    }
}
