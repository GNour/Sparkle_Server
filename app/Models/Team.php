<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'manager_id',
        'leader_id',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, "manager_id");
    }

    public function leader()
    {
        return $this->belongsTo(User::class, "leader_id");
    }

    public function members()
    {
        return $this->hasMany(User::class);
    }
}
