<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildWorksheetAssignment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'child_profile_id',
        'worksheet_id',
        'status',
        'assigned_at',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function childProfile(): BelongsTo
    {
        return $this->belongsTo(ChildProfile::class);
    }

    public function worksheet(): BelongsTo
    {
        return $this->belongsTo(Worksheet::class);
    }
}
