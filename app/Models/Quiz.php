<?php

namespace App\Models;

use App\Models\Course;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'title',
        'weight',
        'course_id',
        'limit',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, "course_id");
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, "users_take_quizzes")
            ->as("details")
            ->withPivot("grade", "completed")
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsToMany(User::class, "users_take_quizzes")
            ->as("details")
            ->wherePivot("user_id", auth()->user()->id)
            ->withPivot("grade", "completed")
            ->withTimestamps();
    }
}
