<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'age',
        'description',
        'category',
        'route_name',
        'thumbnail_path',
        'is_visible',
    ];

    public function childPermissions(): HasMany
    {
        return $this->hasMany(ChildGamePermission::class);
    }
}
