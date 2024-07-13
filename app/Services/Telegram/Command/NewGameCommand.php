<?php

namespace App\Services\Telegram\Command;

use App\Models\Game;
use App\Services\Game\Game as GameService;
use App\Services\Game\NewGame;
use App\Services\Telegram\TgDTOService;
use App\Services\Telegram\TgMessage;
use App\Services\Telegram\TgMessageService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class NewGameCommand
{
    use TgMessageService;

    /*private array $data;
    public function __construct()
    {
        $this->data = TgDTOService::$tgData;
    }*/

    public function gameList(): void
    {
        $this->pagination();

        $newGame = new NewGame();
        $obj = $newGame->list(TgDTOService::$tgData['user_id'], TgDTOService::$tgData['pagination']);

        if(isset($obj['list']))
            $this->newGame($obj['list']);

        if(isset($obj['exists']))
            $this->existsGameMessage($obj['exists']);

    }

    private function newGame($obj): void
    {
        $this->setText('Выберите квест:');

        $arr = [];
        foreach($obj as $value){
            $arr[] = [
                'text' => '#'. $value->id . ' ' .$value->name,
                'callback_data' => 'description_game.' . $value->id,
            ];
        }

        $this->createButton(array_chunk($arr, 2));

        $this->pushButton($this->paginationButton());

        $this->pushButton([
            [
                'text' => '[X] Закрыть',
                'callback_data' => 'delete_callback',
            ]
        ]);

        $this->send();
    }

    private function pagination()
    {
        TgDTOService::$tgData['pagination']['count'] = config('telegram.quest_lines_view_count');

        if (isset(TgDTOService::$tgData['page'])) {
            TgDTOService::$tgData['pagination']['page'] = TgDTOService::$tgData['page'];
        } else {
            TgDTOService::$tgData['pagination']['page'] = 0;
        }

        TgDTOService::$tgData['pagination']['next_page'] = TgDTOService::$tgData['pagination']['page'] + 1;
        TgDTOService::$tgData['pagination']['pre_page'] = TgDTOService::$tgData['pagination']['page'] - 1;
        TgDTOService::$tgData['pagination']['total'] = NewGame::count();
    }
    private function paginationButton()
    {
        $paginateButton = [];

        if (TgDTOService::$tgData['pagination']['pre_page'] >= 0) {
            $paginateButton[] = [
                'text' => '<- Назад',
                'callback_data' => 'list_game.' . TgDTOService::$tgData['pagination']['pre_page'],
            ];
        }

        if (
            TgDTOService::$tgData['pagination']['total'] >
            (TgDTOService::$tgData['pagination']['next_page'] * TgDTOService::$tgData['pagination']['count'])
        ) {
            $paginateButton[] = [
                'text' => 'Вперед ->',
                'callback_data' => 'list_game.' . TgDTOService::$tgData['pagination']['next_page'],
            ];
        }

        return $paginateButton;
    }

    public function existsGameMessage(Game $currentGame)
    {
        $this->setText('У вас есть не закоченые игры ' . PHP_EOL . $currentGame->questionLine->name);
        $this->createButton([[
            [
                'text' => 'Закончить игру',
                'callback_data' => 'finish_game',
            ],
            [
                'text' => 'Продолжить',
                'callback_data' => 'continue',
            ]
        ]]);
        $this->send();
    }

    public function notExistsGameMessage()
    {
        $this->setText('У Вас нет созданых игр');
        $this->send();
    }

    public function answer(): void
    {
        $newGame = new NewGame();
        $currentGame = GameService::checkCurrentGameFromUser(TgDTOService::$tgData['user_id']);

        $users = GameService::getGameUsers();

        if(!$users[TgDTOService::$tgData['user_id']]['capitan']){
            $this->setText('Отвечать может только капитан');
            $this->send();
            die();
        }

        if(!is_null($currentGame)){
            $question = $newGame->nexQuestion($currentGame->id);
            if(!$question){
                $this->finishGame($currentGame);
            }

            if(GameService::checkAnswer($question['question_id'], TgDTOService::$tgData['text']) || TgDTOService::$tgData['text'] == '*'){
                $newGame->correctAnswer($currentGame, $question);

                $next = $newGame->nexQuestion($currentGame->id);

                if($next){
                    (new TgMessage())->answer($question);
                } else {
                    $this->finishGame($currentGame);
                }
            } else {
                $this->setText('Ответ неверный, попробуйте еще раз');
                $this->send();
            }

        }
    }

    private function finishGame(Game $game)
    {
        (new NewGame())->finishGame($game);

        (new TgMessage())->finishGame();

    }

}
