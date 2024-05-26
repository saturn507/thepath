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
            $newGame->createGame($this->data);
            $this->delete();

            $text = "Новая игра создана." . PHP_EOL .
                "Вы можете добавить в каманду еще 3 участников" . PHP_EOL .
                "командой /my_team";
            $this->setText($text);

            $this->createButton([[
                [
                    'text' => 'Получить первую точку',
                    'callback_data' => 'start_game',
                ]
            ]]);

            $this->send();
        } else {
            (new NewGameCommand($this->data))->existsGameMessage($obj);
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

    public function startGame()
    {
        $newGame = new NewGame();
        $obj = $newGame->checkCurrentGameFromUser($this->data['user_id']);

        if(!is_null($obj)){
            $data = $newGame->nexQuestion($obj->id,true);
            $this->delete();

            $text = "Необходимо пройти по адресу." . PHP_EOL .
                $data['location'] . PHP_EOL .
                "и ответить на вопрос" . PHP_EOL . $data['question'];
            $this->setText($text);
            $this->send();
        } else {
            (new NewGameCommand($this->data))->notExistsGameMessage();
        }
    }
}
