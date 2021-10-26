<?php

namespace App\Models;

use App\Models\Article;
use App\Models\Note;
use App\Models\Task;
use App\Models\Team;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'profile_picture',
        'gender',
        'role',
        'position',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [];

    public function team()
    {
        return $this->belongsTo(Team::class, "team_id");
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, "user_tasks", "user_id", "task_id", "id")
            ->as("userTask")
            ->withPivot("deadline", 'completed')
            ->withTimestamps();
    }

    public function videos()
    {
        return $this->belongsToMany(Video::class, "users_watch_videos", "user_id", "video_id", "id")
            ->as("userVideo")
            ->withPivot('completed', 'left_at')
            ->withTimestamps();
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, "users_read_articles", "user_id", "article_id", "id")
            ->as("userArticle")
            ->withPivot('completed')
            ->withTimestamps();
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, "users_take_courses", "user_id", "course_id", "id")
            ->as("userCourse")
            ->withPivot('completed', 'grade')
            ->withTimestamps();
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, "users_take_quizzes", "user_id", "quiz_id", "id")
            ->as("userQuiz")
            ->withPivot('completed', 'grade')
            ->withTimestamps();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
