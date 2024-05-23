<?php

namespace App\Services\Game;

use App\Models\Game;
use App\Models\QuestLine;

class NewGame
{

    public function list($data)
    {
        $currentGame = $this->checkCurrentGameFromUser($data['user_id']);

        if(!is_null($currentGame))
            return ['exists' => $currentGame];

        $count = 2;
        $page = $data['page'] ?? 1;

        return [
            'new' =>
                QuestLine::where('act', true)
                    ->orderByDesc('id')
                    ->limit($count)
                    ->offset($page * $count)
                    ->get()
        ];
    }

    public function checkCurrentGameFromUser($userId)
    {
        $currentGame = Game::with('users', function($query) use ($userId){
            return  $query->where('id', $userId);
        })
            ->where('act', true)
            ->whereNull('finish_at')
            ->first();

        if($currentGame)
            return $currentGame;

        return null;
    }

    public function descriptionGame($hash): QuestLine
    {
       return QuestLine::where('hash', $hash)->first();
    }

}
