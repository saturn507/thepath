<?php

namespace App\Services\Telegram;

use App\Services\Game\NewGame;
use App\Services\Telegram\Callback\GameCallback;
use App\Services\Telegram\Callback\MyTeamCallback;
use App\Services\Telegram\Command\MyTeamCommand;
use App\Services\Telegram\Command\NewGameCommand;

class TgCallback
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

        $this->data['callback'] = $callback[0];
        $this->data['callback_data'] = $callback;

        match ($this->data['callback']) {
            'create_game' => (new GameCallback($this->data))->createGame(),
            'start_game' => (new GameCallback($this->data))->startGame(),
            'description_game' => (new GameCallback($this->data))->descriptionGame(),
            'list_game' => (new GameCallback($this->data))->listGame(),
            'delete_callback' => $this->deleteCallback(),
            'finish_game' => (new GameCallback($this->data))->finishGame(),
            'next_question' => (new GameCallback($this->data))->nextQuestion(),
            'my_team_user_add' => $this->missCommand(),
            'my_team_user_delete' => (new MyTeamCallback($this->data))->userTeamDelete(),
            'my_team_user_capitan_change' => $this->missCommand(),
            default => $this->missCommand(),
        };
    }

    private function missCommand(): void
    {
        $this->setText('Такой команды не существует');
        $this->send();
    }

    private function deleteCallback()
    {
        $this->delete();
    }

}
