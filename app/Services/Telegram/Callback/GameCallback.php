<?php

namespace App\Services\Telegram\Callback;

use App\Models\Game;
use App\Services\Game\NewGame;
use App\Services\Telegram\Command\NewGameCommand;
use App\Services\Telegram\TgMessageService;
use Carbon\Carbon;

class GameCallback
{
    use TgMessageService;

    private array $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function descriptionGame()
    {
        $res = (new NewGame)->descriptionGame($this->data['callback_data'][1]);

        if(!is_null($res)){
            $this->setText($res->description);
            $this->createButton(array_chunk([
                [
                    'text' => '[X] Закрыть',
                    'callback_data' => 'delete_callback',
                ],
                [
                    'text' => 'Начать игру -->',
                    'callback_data' => 'create_game.' . $res->id,
                ]
            ], 2));
            $this->send();
        }
    }

    public function createGame()
    {
        $newGame = new NewGame();
        $obj = $newGame->checkCurrentGameFromUser($this->data['user_id']);

        if(is_null($obj)){
            $game = Game::create([
                'quest_line_id' => $this->data['callback_data'][1],
                'start_at' => Carbon::now()
            ]);

            $game->userGame()->insert([
                'game_id' => $game->id,
                'user_id' => $this->data['user_id'],
                'capitan' => true,
                'confirmed' => true
            ]);

            $this->delete();
        } else {
            (new NewGameCommand($this->data))->existsGameMessage();
        }
    }

    public function listGame()
    {
        $this->delete();

        if(isset($this->data['callback_data'][1])){
            $this->data['page'] = $this->data['callback_data'][1];
        }

        (new NewGameCommand($this->data))->listGame();
    }

    public function finishGame()
    {
        $this->delete();

        $newGame = new NewGame();
        $obj = $newGame->checkCurrentGameFromUser($this->data['user_id']);
        if (!is_null($obj)){
            Game::query()
                ->where('id', $obj->id)
                ->update([
                    'act' => false,
                ]);
        }
    }
}
