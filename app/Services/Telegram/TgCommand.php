<?php

namespace App\Services\Telegram;

use App\Services\Telegram\Callback\GameCallback;
use App\Services\Telegram\Callback\MyTeamCallback;
use App\Services\Telegram\Command\MyTeamCommand;
use App\Services\Telegram\Command\NewGameCommand;
use App\Services\Telegram\TgDTOService;

class TgCommand
{
    use TgMessageService;

    private array $data;
    public function __construct()
    {
        $this->data = TgDTOService::$tgData;
    }

    public function run(): void
    {
        match ($this->data['command']) {
    /******* КОМАНДЫ **************************************************************************************************/
            'new_game' => (new NewGameCommand())->gameList(), //
            'my_team' => (new MyTeamCommand())->myTeamList(), //
            'start' => $this->startCommand(),
    /****** ОБРАТНАЯ СВЯЗЬ ********************************************************************************************/
        /** Настройки игры ****************************************************************************************/
            'create_game' => (new GameCallback())->createGame(), //
            'start_game' => (new GameCallback())->startGame(), //
            'description_game' => (new GameCallback())->descriptionGame(), //
            'list_game' => (new GameCallback())->gameList(), //
            'delete_callback' => $this->deleteCallback(),
            'finish_game' => (new GameCallback())->finishGame(), //
            'next_question' => (new GameCallback())->nextQuestion(), //
        /** Настройки команды *************************************************************************************/
            'my_team_user_add_enter' => (new MyTeamCallback(TgDTOService::$tgData))->userTeamAddEnter(),
            'my_team_user_add' => (new MyTeamCallback(TgDTOService::$tgData))->userTeamAdd(),
            'my_team_user_delete' => (new MyTeamCallback(TgDTOService::$tgData))->userTeamDelete(),
            'my_team_user_capitan_change' => $this->missCommand(),
            'my_team_user_confirm' => (new MyTeamCallback(TgDTOService::$tgData))->userTeamConfirm(),
            'my_team_user_refuse' => (new MyTeamCallback(TgDTOService::$tgData))->userTeamRefuse(),
            default => $this->missCommand(),
        };

        return;
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
