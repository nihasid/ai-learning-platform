<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChildProfile extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'parent_id',
        'name',
        'birthdate',
        'avatar_color',
        'audio_guidance_enabled',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'audio_guidance_enabled' => 'boolean',
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

    public function worksheets(): HasMany
    {
        return $this->hasMany(ChildWorksheetAssignment::class);
    }

    public function worksheetAssignments(): HasMany
    {
        return $this->hasMany(ChildWorksheetAssignment::class);
    }

    public function gamePermissions()
    {
        return $this->hasMany(ChildGamePermission::class, 'child_id');
    }

    public function allowedGames()
    {
        return $this->belongsToMany(Game::class, 'child_game_permissions', 'child_id', 'game_id')
            ->wherePivot('is_allowed', true);
    }
}
