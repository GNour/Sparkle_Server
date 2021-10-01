<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'manager_id',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, "manager_id");
    }

    public function leader()
    {
        return $this->belongsTo(User::class, "leader_id");
    }

    public function tasks()
    {
        return $this->BelongsToMany(Task::class, "team_tasks", "task_id", "team_id", "id")
            ->as("tasks")
            ->withPivot("deadline")
            ->withTimestamps();
    }

    public function members()
    {
        return $this->hasMany(User::class);
    }
}
