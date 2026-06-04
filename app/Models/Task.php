<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Task extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
 
    protected $fillable = [
        'user_id', /*'category_id', */'parent_task_id',
        'title', 'description', 'status', 'priority',
        'estimated_minutes', 'actual_minutes',
        'due_at', 'started_at', 'completed_at',
        'ai_rank', 'ai_score', 'ai_reasoning',
    ];
 
    protected function casts(): array
    {
        return [
            'due_at'       => 'datetime',
            'started_at'   => 'datetime',
            'completed_at' => 'datetime',
            'ai_reasoning' => 'array',
            'ai_score'     => 'float',
        ];
    }
 
    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
 
    // public function category(): BelongsTo
    // {
    //     return $this->belongsTo(Category::class);
    // }
 
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }
 
    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }
 
    // public function schedules(): HasMany
    // {
    //     return $this->hasMany(TaskSchedule::class);
    // }
 
    // public function feedback(): HasMany
    // {
    //     return $this->hasMany(TaskFeedback::class);
    // }
 
    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
 
    public function scopeOverdue($query)
    {
        return $query->where('due_at', '<', now())->whereNotIn('status', ['completed', 'skipped']);
    }
 
    public function scopeForToday($query)
    {
        return $query->whereBetween('due_at', [now()->startOfDay(), now()->endOfDay()]);
    }
 
    // Accessors
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_at && $this->due_at->isPast() && !in_array($this->status, ['completed', 'skipped']);
    }
 
    public function getUrgencyLabelAttribute(): string
    {
        return match(true) {
            $this->priority === 'critical'              => 'Critical',
            $this->due_at && $this->due_at->isToday()  => 'Due today',
            $this->due_at && $this->due_at->isTomorrow()=> 'Due tomorrow',
            $this->is_overdue                           => 'Overdue',
            default                                     => ucfirst($this->priority),
        };
    }
}