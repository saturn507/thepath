<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            GameToUser::class,
            'game_id',
            'id',
            'id',
            'user_id');
    }

    public function userGame(): HasMany
    {
        return $this->hasMany(GameToUser::class, 'id', 'game_id');
    }

    public function questionLine(): HasOne
    {
        return $this->hasOne(QuestLine::class, 'id', 'quest_line_id');
    }

}
