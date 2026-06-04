<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    /** @use HasFactory<\Database\Factories\UserPreferenceFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 'peak_hours', 'avoid_hours',
        'max_daily_tasks', 'focus_block_minutes', 'break_minutes', 'notifications_on',
    ];

    protected function casts(): array
    {
        return [
            'peak_hours'         => 'array',
            'avoid_hours'        => 'array',
            'notifications_on'   => 'boolean',
        ];
    }
 
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
