<?php

namespace App\Services\Telegram\Callback;

use App\Services\Game\NewGame;
use App\Services\Telegram\TgMessageService;

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
                    'text' => 'Закрыть',
                    'callback_data' => 'delete_callback',
                ],
                [
                    'text' => 'Начать игру',
                    'callback_data' => 'create_game.' . $res->hash,
                ]
            ], 2));
            $this->send();
        }
    }

    public function createGame()
    {

    }
}
