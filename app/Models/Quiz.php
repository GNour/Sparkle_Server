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
}
