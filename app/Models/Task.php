<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'taskable_id',
        'taskable_type',
        'created_by',
    ];

    public function taskable()
    {
        return $this->morphTo();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, "user_tasks")->withPivot(["deadline", "completed"])->withTimestamps();
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, "team_tasks")->withPivot(["deadline", "completed"])->withTimestamps();
    }

}
