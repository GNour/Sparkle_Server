<?php

namespace App\Models;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'description',
        'course_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, "course_id");
    }

    public function users()
    {
        return $this->belongsToMany(User::class, "users_read_articles")
            ->as("details")
            ->withPivot("completed")
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsToMany(User::class, "users_read_articles")
            ->as("details")
            ->wherePivot("user_id", auth()->user()->id)
            ->withPivot("completed")
            ->withTimestamps();
    }
}
