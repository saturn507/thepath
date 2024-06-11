<?php

namespace App\Services\Telegram\Command;

use App\Models\Game;
use App\Services\Game\Game as GameService;
use App\Services\Game\NewGame;
use App\Services\Telegram\TgMessageService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class NewGameCommand
{
    use TgMessageService;

    private array $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function gameList(): void
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
        $this->data['pagination']['count'] = config('telegram.quest_lines_view_count');

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
        $obj = GameService::checkCurrentGameFromUser($this->data['user_id']);

        if(!is_null($obj)){
            $question = $newGame->nexQuestion($obj->id);
            //dd(Storage::disk('point')->url($question['question_img']));
            if(!$question){
                $this->finishGame($obj);
            }

            if(GameService::checkAnswer($question['question_id'], $this->data['text']) || $this->data['text'] == '*'){
                $newGame->correctAnswer($obj, $question);

                $next = $newGame->nexQuestion($obj->id);

                if($next){

                    $text = "Поздравляем! Вы правильно ответили.";

                    $button[] = [
                        'text' => 'Следующее задание',
                        'callback_data' => 'next_question',
                    ];

                    if(!is_null($question['historical_reference'])){
                        $button[] = [
                            'text' => 'Получить историческую справку',
                            'callback_data' => 'history.' . $question['question_id'],
                        ];
                    }

                    $this->createButton(array_chunk($button, 2));

                    if(!is_null($question['answer_img'])){
                        $url = Storage::disk('point')->url($question['answer_img']);
                        $this->setImg($url);
                    }

                    $this->setText($text);
                    $this->send();


                    /*$text = "Вам нужно быть здесь: " . PHP_EOL .
                        $next['location'] . '.' . PHP_EOL .
                        "Ответьте на вопрос:" . PHP_EOL . $next['question'];
                    if(!is_null($next['question_img'])){
                        $url = Storage::disk('point')->url($next['question_img']);
                        $this->setImg($url);
                    }

                    $this->setText($text);
                    $this->send();*/
                } else {
                    $this->finishGame($obj);
                }
            } else {
                $this->setText('Ответ неверный, попробуйте еще раз');
                $this->send();
            }

        }
    }

    private function finishGame(Game $game)
    {
        $newGame = new NewGame();
        $newGame->finishGame($game);
        $text = "Вы выполнили все задания." . PHP_EOL .
            "Поздравляем!";
        $this->setText($text);
        $this->send();
    }

}
