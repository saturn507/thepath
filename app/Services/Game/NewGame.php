<?php

namespace App\Services\Game;

use App\Models\Game;
use App\Models\QuestLine;

class NewGame
{

    public function list($userId)
    {
        $currentGame = $this->checkCurrentGameFromUser($userId);

        if(!is_null($currentGame))
            return ['exists' => $currentGame];

        return ['new' => QuestLine::where('act', true)->orderByDesc('id')->get()];
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

    public function descriptionGame($hash): ?string
    {
       $res = QuestLine::select('description')->where('hash', $hash)->first();

       if($res)
           return $res->description;

       return null;
    }

}
