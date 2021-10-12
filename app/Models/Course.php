<?php

namespace App\Models;

use App\Models\Article;
use App\Models\Quiz;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by',
    ];

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function tasks()
    {
        return $this->morphMany(Task::class, "taskable");
    }
}
