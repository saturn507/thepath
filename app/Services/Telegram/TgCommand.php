<?php

namespace App\Services\Telegram;

use App\Services\Telegram\Command\NewGame;

class TgCommand
{
    use TgMessageService;

    private array $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function run()
    {
        match ($this->data['command']) {
            'new_game' => (new NewGame($this->data))->run(),
            'my_team' => dd('my_team'),
            default => $this->missCommand(),
        };
    }

    private function missCommand()
    {
        $this->setText('Такой команды не существует');
        $this->send();
    }
}
