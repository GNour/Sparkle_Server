<?php

namespace App\Models;

use App\Models\Answer;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'question',
        'answer',
        'weight',
        'quiz_id',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, "quiz_id");
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
