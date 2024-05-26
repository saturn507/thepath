<?php

namespace App\Services\Game;

use App\Models\Game;
use App\Models\GameToPoint;
use App\Models\QuestLine;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use DB;

class NewGame
{

    public function list($data)
    {
        $currentGame = $this->checkCurrentGameFromUser($data['user_id']);

        if(!is_null($currentGame))
            return ['exists' => $currentGame];

        $count = $data['pagination']['count'];
        $page = $data['pagination']['page'];

        return [
            'list' =>
                QuestLine::where('act', true)
                    ->orderByDesc('id')
                    ->limit($count)
                    ->offset($page * $count)
                    ->get()
        ];
    }

    public static function count()
    {
        return QuestLine::where('act', true)->count();
    }

    public function checkCurrentGameFromUser($userId)
    {
        $currentGame = Game::query()
            ->with('questionLine')
            ->whereHas(
                'users',
                fn(Builder $builder) => $builder->where('id', $userId)
            )
            ->where('act', true)
            ->whereNull('finish_at')
            ->first();

        if($currentGame)
            return $currentGame;

        return null;
    }

    public function descriptionGame($id): QuestLine
    {
       return QuestLine::query()->where('id', $id)->first();
    }

    public function createGame($data)
    {
        DB::transaction(function () use ($data) {
            $game = Game::query()->create([
                'quest_line_id' => $data['callback_data'][1]
            ]);

            $points = QuestLineToPoint::query()
                ->select('point_id')
                ->where('quest_line_id', $game->quest_line_id)
                ->orderBy('id')
                ->get()
                ->pluck('point_id');

            $pointArray = [];
            foreach ($points as $point){
                $pointArray[] = [
                    'game_id' => $game->id,
                    'point_id' => $point->point_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }

            GameToPoint::insert($pointArray);

            $game->userGame()->insert([
                'game_id' => $game->id,
                'user_id' => $data['user_id'],
                'capitan' => true,
                'confirmed' => true
            ]);
        });
    }

    public function startGame($gameId)
    {
        $game = Game::query()->update(['start_at' => Carbon::now()]);

        $ql = QuestLine::query();
    }

}
