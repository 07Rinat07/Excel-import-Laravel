<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = [
        'title',
    ];

    protected $table = 'types';

    public function templates()
    {
        return $this->hasMany(ExcelTemplate::class, 'type_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'type_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'type_id');
    }
}
