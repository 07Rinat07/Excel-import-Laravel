<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'type_id',
        'title',
        'created_at_time',
        'contracted_at',
        'deadline',
        'is_chain',
        'is_on_time',
        'has_outsource',
        'has_investors',
        'worker_count',
        'service_count',
        'payment_first_step',
        'payment_second_step',
        'payment_third_step',
        'payment_forth_step',
        'comment',
        'effective_value',
        'task_id',
        'template_id',
        'row_index',
        'sheet_name',
        'sheet_index',
    ];

    protected $table = 'projects';

    protected $with = ['type'];

    protected $dates = ['created_at', 'contracted_at', 'deadline'];

    protected $casts = [
        'is_chain' => 'boolean',
        'is_on_time' => 'boolean',
        'has_outsource' => 'boolean',
        'has_investors' => 'boolean',
        'worker_count' => 'integer',
        'service_count' => 'integer',
        'payment_first_step' => 'integer',
        'payment_second_step' => 'integer',
        'payment_third_step' => 'integer',
        'payment_forth_step' => 'integer',
        'effective_value' => 'decimal:2',
        'sheet_index' => 'integer',
    ];

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->whereHas('task', function (Builder $taskQuery) use ($user) {
            $taskQuery->where('user_id', $user->id);
        });
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function template()
    {
        return $this->belongsTo(ExcelTemplate::class, 'template_id');
    }

    public function values()
    {
        return $this->hasMany(ProjectValue::class, 'project_id');
    }
}
