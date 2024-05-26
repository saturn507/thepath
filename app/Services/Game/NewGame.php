<?php

namespace App\Services\Game;

use App\Models\Game;
use App\Models\GameToPoint;
use App\Models\Location;
use App\Models\QuestLine;
use App\Models\QuestLineToPoint;
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

    public function getNextLocation($gameId)
    {
        return Game::query()
            ->with('points', fn($builder) =>
                $builder->where('completed', false)
                    ->orderBy('game_to_points.id')
                    ->limit(1)
            )
            //->with('points.location')
            ->whereHas(
                'currentPoint'
            )
            ->where('id', $gameId)
            ->whereNull('finish_at')
            ->first();
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
                ->pluck('point_id');

            $pointArray = [];
            foreach ($points as $point){
                $pointArray[] = [
                    'game_id' => $game->id,
                    'point_id' => $point,
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

    public function nexQuestion($gameId, $start = false)
    {
        if($start){
            Game::query()
                ->where('id', $gameId)
                ->update(['start_at' => Carbon::now()]);
        }

        $point = $this->getNextLocation($gameId);

        $data = [
            'location' => Location::find($point->points[0]->location_id)->name,
            'question_id' => $point->points[0]->id,
            'question' => $point->points[0]->question,
            'answer' => $point->points[0]->answer,
        ];

        return $data;
    }

    public function correctAnswer($game, $answer)
    {
        GameToPoint::query()
            ->where('game_id', $game->id)
            ->where('point_id', $answer['question_id'])
            ->update([
                'completed' => true,
                'answer' => $answer['answer']
            ]);
    }

}
