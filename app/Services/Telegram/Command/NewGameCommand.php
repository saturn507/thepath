<?php

namespace App\Services\Telegram\Command;

use App\Models\Game;
use App\Services\Game\NewGame;
use App\Services\Telegram\TgMessageService;

class NewGameCommand
{
    use TgMessageService;

    private array $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function listGame(): void
    {
        $this->pagination();

        $newGame = new NewGame();
        $obj = $newGame->list($this->data);

        if(isset($obj['list']))
            $this->newGame($obj['list']);

        if(isset($obj['exists']))
            $this->existsGameMessage($obj['exists']);

    }

    private function newGame($obj): void
    {
        $this->setText('Выбирите квест:');

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
        $this->data['pagination']['count'] = 4;

        if (isset($this->data['page'])) {
            $this->data['pagination']['page'] = $this->data['page'];
        } else {
            $this->data['pagination']['page'] = 0;
        }

        $this->data['pagination']['next_page'] = $this->data['pagination']['page'] + 1;
        $this->data['pagination']['pre_page'] = $this->data['pagination']['page'] - 1;
        $this->data['pagination']['total'] = NewGame::count();
    }
    private function paginationButton()
    {
        $paginateButton = [];

        if ($this->data['pagination']['pre_page'] >= 0) {
            $paginateButton[] = [
                'text' => '<- Назад',
                'callback_data' => 'list_game.' . $this->data['pagination']['pre_page'],
            ];
        }

        if ($this->data['pagination']['total'] > ($this->data['pagination']['next_page'] * $this->data['pagination']['count'])) {
            $paginateButton[] = [
                'text' => 'Вперед ->',
                'callback_data' => 'list_game.' . $this->data['pagination']['next_page'],
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

    public function answer()
    {
        $newGame = new NewGame();
        $obj = $newGame->checkCurrentGameFromUser($this->data['user_id']);

        if(!is_null($obj)){
            $question = $newGame->nexQuestion($obj->id);
            dd($question);
            $answer = mb_strtolower(preg_replace( "/[^a-zA-ZА-Яа-я0-9]/ui", '', $question['answer']));
            $possibleAnswer = mb_strtolower(preg_replace( "/[^a-zA-ZА-Яа-я0-9]/ui", '', $this->data['text']));

            if($answer == $possibleAnswer){
                $newGame->correctAnswer($obj, $question);

                $next = $newGame->nexQuestion($obj->id);

                if($next){
                    $text = "Необходимо пройти по адресу." . PHP_EOL .
                        $next['location'] . PHP_EOL .
                        "и ответить на вопрос" . PHP_EOL . $next['question'];
                    $this->setText($text);
                    $this->send();
                } else {
                    $newGame->finishGame($obj->id);
                    $text = "Вы выполнили все задания." . PHP_EOL .
                        "Поздравляем!";
                    $this->setText($text);
                    $this->send();
                }
            } else {
                $this->setText('Ответ не верный, попробуйте еще раз');
                $this->send();
            }

        }
    }

}
