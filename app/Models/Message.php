<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'from',
        'message',
    ];

    public function to()
    {
        return $this->belongsTo(User::class, "to")->withTrashed();
    }

    public function from()
    {
        return $this->belongsTo(User::class, "from")->withTrashed();
    }
}
