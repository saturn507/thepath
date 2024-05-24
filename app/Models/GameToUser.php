<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameToUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'user_id',
        'capitan',
        'confirmed'
    ];

    protected $table = 'game_to_users';
}
