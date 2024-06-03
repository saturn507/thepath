<?php

namespace App\Services\Game;

use App\Helpers\AnswerHelper;
use App\Models\Point;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Game as GameModel;

class Game
{
    private static ?object $currentGame;
    public static function checkCurrentGameFromUser($userId)
    {
        $currentGame = GameModel::query()
            ->with('questionLine')
            ->whereHas(
                'users',
                fn(Builder $builder) => $builder->where('id', $userId)
            )
            ->where('act', true)
            ->whereNull('finish_at')
            ->first();

        self::$currentGame = $currentGame;

        if($currentGame)
            return $currentGame;

        return null;
    }

    public static function checkAnswer($pointId, $answer)
    {
        return Point::query()
            ->where('id', $pointId)
            ->whereHas(
                'answers',
                fn(Builder $builder) => $builder->where('answer_transformation', AnswerHelper::low($answer))
            )
            ->exists();
    }
}
