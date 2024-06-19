<?php

namespace App\Services\Telegram;

use App\Services\Telegram\Command\MyTeamCommand;
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
            'my_team' => (new MyTeamCommand($this->data))->myTeamList(),
            'start' => $this->startCommand(),
            default => $this->missCommand(),
        };

        return;
    }

    private function missCommand(): void
    {
        $this->setText('Такой команды не существует');
        $this->send();
    }

    private function startCommand(): void
    {
        $text = 'Привет!' . PHP_EOL . PHP_EOL;
        $text .= 'Мы приготовили для тебя квесты по Петербургу. Надеемся, что тебе понравится, что ты узнаешь и увидишь что-нибудь новое и интересное для себя.' . PHP_EOL . PHP_EOL;
        $text .= 'Задание — это что-нибудь посчитать, например. Бонус — найти по фотографии какой-то фрагмент в реальности. Подсказок нет, но если дан неправильный ответ, можно ещё раз ответить.' . PHP_EOL . PHP_EOL;
        $text .= 'Удачи тебе и вперёд, к приключениям!';

        $this->setText($text);
        $this->send();
    }
}
