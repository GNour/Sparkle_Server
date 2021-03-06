<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'taskable_id',
        'taskable_type',
        'created_by',
        'assigned',
    ];

    public function taskable()
    {
        return $this->morphTo();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, "user_tasks")
            ->as("details")
            ->withPivot("deadline", "completed")
            ->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by", "id");
    }

}
