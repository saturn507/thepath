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

    public function run(): void
    {
        $callback = explode('.', $this->data['callback']);

        $res = match ($callback[0]) {
            'create_game' => (new NewGame)->descriptionGame($callback[1]),
            'description_game' => (new NewGame)->descriptionGame($callback[1]),
            default => $this->missCommand(),
        };

        if(!is_null($res)){
            $this->setText($res->description);
            $this->createButton(array_chunk([
                [
                    'text' => 'Назад',
                    'callback_data' => 'description_game',
                ],
                [
                    'text' => 'Начать игру',
                    'callback_data' => 'create_game.' . $res->hash,
                ]
            ], 2));
            $this->send();
        }


    }

    private function missCommand(): void
    {
        $this->setText('Такой команды не существует');
        $this->send();
    }
}
