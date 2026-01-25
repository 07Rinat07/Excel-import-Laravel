<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'file_id',
        'status',
        'type',
        'type_id',
        'template_id',
        'column_map',
        'total_rows',
        'imported_rows',
    ];

    protected $table = 'tasks';

    protected $casts = [
        'status' => 'integer',
        'column_map' => 'array',
        'total_rows' => 'integer',
        'imported_rows' => 'integer',
    ];

    const STATUS_PENDING = 0;

    const STATUS_PROCESS = 1;

    const STATUS_SUCCESS = 2;

    const STATUS_ERROR = 3;

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Ожидает сопоставления колонок',
            self::STATUS_PROCESS => 'Импорт в процессе обработки',
            self::STATUS_SUCCESS => 'Импорт данных успешно прошел',
            self::STATUS_ERROR => 'Ошибка валидации во время импорта',
        ];
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id', 'id');
    }

    public function failedRows()
    {
        return $this->hasMany(FailedRow::class, 'task_id', 'id');
    }

    public function typeModel()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function template()
    {
        return $this->belongsTo(ExcelTemplate::class, 'template_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'task_id');
    }
}
