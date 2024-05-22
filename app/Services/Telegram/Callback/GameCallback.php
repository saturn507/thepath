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
        $callback = implode('.', $this->data['callback']);

        $res = match ($this->data['command']) {
            'create_game' => (new NewGame)->descriptionGame($callback[1]),
            'description_game' => dd('my_team'),
            default => $this->missCommand(),
        };

        if(!is_null($res)){
            $this->setText($res->description);
            $this->send();
        }


    }

    private function missCommand(): void
    {
        $this->setText('Такой команды не существует');
        $this->send();
    }
}
