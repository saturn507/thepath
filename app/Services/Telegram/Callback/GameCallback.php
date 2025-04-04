<?php

namespace App\Services\Telegram\Callback;

use App\Models\Game as GameModel;
use App\Models\Location;
use App\Services\Game\NewGame;
use App\Services\Telegram\Command\NewGameCommand;
use App\Services\Telegram\TgDTOService;
use App\Services\Telegram\TgMessage;
use App\Services\Telegram\TgMessageService;
use Carbon\Carbon;
use App\Services\Game\Game as GameService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class GameCallback
{
    use TgMessageService;

    public function descriptionGame(): void
    {
        $questDescription = (new NewGame)->descriptionGame(TgDTOService::$tgData['callback_data'][1]);

        if (!is_null($questDescription)) {
            $this->setText($questDescription->description);
            $this->createButton(array_chunk([
                [
                    'text' => '[X] Закрыть',
                    'callback_data' => 'delete_callback',
                ],
                [
                    'text' => 'Начать игру -->',
                    'callback_data' => 'create_game.' . $questDescription->id,
                ]
            ], 2));
            $this->send();
        }
    }

    public function createGame(): void
    {
//        $currentGame = GameService::checkCurrentGameFromUser(TgDTOService::$tgData['user_id']);
//
//        if (!$currentGame)
//            (new NewGameCommand())->notExistsGameMessage();

        (new NewGame())->createGame(TgDTOService::$tgData['callback_data'][1], TgDTOService::$tgData['user_id']);
        $this->delete();

        $text = "Новая игра создана." . PHP_EOL .
            "Вы можете добавить в команду еще 3-x участников" . PHP_EOL .
            "командой /my_team";
        $this->setText($text);

        $this->createButton([[
            [
                'text' => 'Получить первую точку',
                'callback_data' => 'start_game',
            ]
        ]]);

        $this->send();
    }

    public function gameList(): void
    {
        $this->delete();

        if (isset(TgDTOService::$tgData['callback_data'][1])) {
            TgDTOService::$tgData['page'] = TgDTOService::$tgData['callback_data'][1];
        }

        (new NewGameCommand())->gameList();
    }

    public function finishGame(): void
    {
        $this->delete();

        $currentGame = GameService::checkCurrentGameFromUser(TgDTOService::$tgData['user_id']);
        if (!is_null($currentGame)) {
            GameModel::query()
                ->where('id', $currentGame->id)
                ->update([
                    'act' => false,
                ]);
        }

        $this->setText("Игра закончена");
        $this->send();
    }

    public function startGame(): void
    {
        $this->nextQuestion(true);
    }

    public function nextQuestion($flag = false): void
    {
        $currentGame = GameService::checkCurrentGameFromUser(TgDTOService::$tgData['user_id']);

        if (!$currentGame)
            (new NewGameCommand())->notExistsGameMessage();


        $data = (new NewGame())->nexQuestion($currentGame->id, $flag);

        (new TgMessage())->currentPoint($data);

        //$this->delete();

        /*$text = "Вам нужно быть здесь: " . PHP_EOL .
            $data['location'] . PHP_EOL .
            "Ответьте на вопрос:" . PHP_EOL . $data['question'];

        if (!is_null($data['question_img'])) {
            $url = Storage::disk('point')->url($data['question_img']);
            $this->setImg($url);
        }

        $this->setText($text);
        $this->send();*/
    }

    public function pushHistory()
    {
        $text =  Location::query()
            ->select('historical_reference')
            ->where('id', TgDTOService::$tgData['callback_data'][1])
            ->first()
            ->historical_reference;

        $this->setText($text);
        $this->send();
    }
}
