<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'quest_line_id',
        'act',
        'start_at',
        'finish_at',
        'team_name'
    ];

    public function user()
    {
        return $this->hasManyThrough(User::class, GameToUser::class);
    }

    public function userGame()
    {
        return $this->hasMany(GameToUser::class, 'id', 'game_id');
    }

}
