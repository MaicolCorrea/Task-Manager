<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'user_id'
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function getTasksCount()
    {
        return $this->tasks()->count();
    }

    public function getCompletedTasksCount()
    {
        return $this->tasks()->where('status', 'Completada')->count();
    }

    public function getProgressPercentage()
    {
        $total = $this->getTasksCount();
        if ($total === 0) return 0;
        
        return round(($this->getCompletedTasksCount() / $total) * 100);
    }
}
