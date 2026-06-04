<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory, HasUuids;
 
    protected $fillable = ['user_id', 'name', 'color', 'icon'];
 
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
 
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
