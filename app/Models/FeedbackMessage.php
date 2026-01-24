<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackMessage extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
