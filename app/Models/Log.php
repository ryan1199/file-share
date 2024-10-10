<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'loggable_id',
        'loggable_type',
        'message',
        'user_id',
        'user_name',
        'user_slug',
    ];

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
