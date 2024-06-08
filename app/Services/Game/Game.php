<?php

namespace App\Services\Game;

use App\Helpers\AnswerHelper;
use App\Models\GameToPoint;
use App\Models\GameToUser;
use App\Models\Point;
use App\Services\Telegram\TgDTOService;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Game as GameModel;
use Illuminate\Support\Facades\Cache;

class Game
{
    private static ?object $currentGame = null;
    public static function checkCurrentGameFromUser($userId = null)
    {
        if(!is_null(self::$currentGame))
            return self::$currentGame;

        if(is_null($userId)){
            $userId = TgDTOService::$tgData['user_id'];
        }

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

    public static function getGameUsers()
    {
        $cacheKey = 'users_game_' . self::$currentGame->id;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        } else {
            $users = GameToUser::with('users.tgUser')
                ->where('game_id', self::$currentGame->id)
                ->get();

            $u = [];

            foreach($users as $user){
                $u[$user->users->id] = [
                    "capitan" => $user->capitan,
                    "confirmed" => $user->confirmed,
                    "first_name" => $user->users->tgUser->first_name,
                    "last_name" => $user->users->tgUser->last_name,
                    "username" => "@" . $user->users->tgUser->username,
                ];
            }

            Cache::put($cacheKey, $u, 60*60*8);

            return $u;
        }
    }
}
