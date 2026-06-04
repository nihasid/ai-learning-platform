<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildProgress extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'child_profile_id',
        'learning_activity_id',
        'status',
        'attempts',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function childProfile(): BelongsTo
    {
        return $this->belongsTo(ChildProfile::class);
    }

    public function learningActivity(): BelongsTo
    {
        return $this->belongsTo(LearningActivity::class);
    }
}
