<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory, HasUuids;

    public const MANAGE_USERS = 'manage_users';
    public const MANAGE_WORKSHEETS = 'manage_worksheets';
    public const VIEW_REPORTS = 'view_reports';

    protected $fillable = ['name', 'label'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
