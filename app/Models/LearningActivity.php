<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningActivity extends Model
{
    use HasFactory, HasUuids;

    public const DOMAINS = ['literacy', 'numeracy', 'motor', 'social_emotional'];

    protected $fillable = [
        'parent_id',
        'title',
        'domain',
        'prompt',
        'audio_prompt',
        'age_min',
        'age_max',
        'button_color',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(ChildProgress::class);
    }
}
