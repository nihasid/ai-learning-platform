<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Worksheet extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public const SUBJECTS = ['math', 'literature', 'general_knowledge', 'activity_book', 'drawing'];
    public const AGE_GROUPS = ['2-3', '4-5', '6-8', '9-12'];

    protected $fillable = [
        'uploaded_by',
        'title',
        'subject',
        'age_group',
        'description',
        'file_path',
        'original_filename',
        'mime_type',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ChildWorksheetAssignment::class);
    }
}
