<?php

namespace App\Services\Telegram;

use App\Services\Telegram\Command\NewGameCommand;

class TgCommand
{
    use TgMessageService;

    private array $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function run(): void
    {
        match ($this->data['command']) {
            'new_game' => (new NewGameCommand($this->data))->listGame(),
            'my_team' => $this->missCommand(),
            default => $this->missCommand(),
        };

        return;
    }

    private function missCommand(): void
    {
        $this->setText('Такой команды не существует');
        $this->send();
    }
}
