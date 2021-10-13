<?php

namespace App\Models;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'video',
        'title',
        'description',
        'course_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, "course_id");
    }

    public function users()
    {
        return $this->belongsToMany(User::class, "users_watch_videos")
            ->as("details")
            ->withPivot("left_at", "completed")
            ->withTimestamps();
    }
}
