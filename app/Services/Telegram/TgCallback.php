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

        TgDTOService::$tgData['callback'] = $callback[0];
        TgDTOService::$tgData['callback_data'] = $callback;

        match ($this->data['callback']) {
            /** Настройки игры ****************************************************************************************/
            'create_game' => (new GameCallback($this->data))->createGame(),
            'start_game' => (new GameCallback($this->data))->startGame(),
            'description_game' => (new GameCallback($this->data))->descriptionGame(),
            'list_game' => (new GameCallback($this->data))->gameList(),
            'delete_callback' => $this->deleteCallback(),
            'finish_game' => (new GameCallback($this->data))->finishGame(),
            'next_question' => (new GameCallback($this->data))->nextQuestion(),
            /** Настройки команды *************************************************************************************/
            'my_team_user_add_enter' => (new MyTeamCallback($this->data))->userTeamAddEnter(),
            'my_team_user_add' => (new MyTeamCallback($this->data))->userTeamAdd(),
            'my_team_user_delete' => (new MyTeamCallback($this->data))->userTeamDelete(),
            'my_team_user_capitan_change' => $this->missCommand(),
            'my_team_user_confirm' => (new MyTeamCallback($this->data))->userTeamConfirm(),
            'my_team_user_refuse' => (new MyTeamCallback($this->data))->userTeamRefuse(),
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
