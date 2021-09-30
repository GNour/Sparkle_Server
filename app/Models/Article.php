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
}
