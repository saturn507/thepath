<?php

namespace App\Services\Telegram;

use App\Services\Telegram\TgSender;
use Illuminate\Support\Facades\Storage;
use App\Services\Game\Game;

class TgMessage
{
    use TgSender;

    private array $users;
    public function __construct()
    {
        $this->users = Game::getGameUsers();
    }
    public function currentPoint($data): void
    {
        $text = "";
        if(!is_null($data['location'])){
            $text .= "Вам нужно быть здесь: " . PHP_EOL . $data['location'] . PHP_EOL;
        } else {
            $text .= "Загадка." . PHP_EOL;
        }

        $text .=  "Ответьте на вопрос:" . PHP_EOL . $data['question'];

        if (!is_null($data['question_img'])) {
            $url = Storage::disk('point')->url($data['question_img']);
            $this->setImg($url);
        }

        $this->setText($text);

        foreach ($this->users as $user){
            if(!$user['confirmed'])
                continue;

            $this->setChatId($user['chat_id']);
            $this->send();
        }
    }

    public function answer($question, $questComplete = false): void
    {
        $text = "Поздравляем! Вы правильно ответили." . PHP_EOL;
        $text .= "Правильный ответ: " . $question['answer'];

        if(!is_null($question['answer_img'])){
            $url = Storage::disk('point')->url($question['answer_img']);
            $this->setImg($url);
        }

        $this->setText($text);

        foreach ($this->users as $user){
            if(!$user['confirmed'])
                continue;

            $this->setChatId($user['chat_id']);

            if(!is_null($question['historical_reference'])){
                $button[] = [
                    'text' => 'Получить историческую справку',
                    'callback_data' => 'history.' . $question['location_id'],
                ];
            }

            if($user['capitan'] && !$questComplete){
                $button[] = [
                    'text' => 'Следующее задание',
                    'callback_data' => 'next_question',
                ];
            }
            if(isset($button)){
                $this->createButton(array_chunk($button, 2));
            }

            $this->send();

            $button = [];
            $this->resetButton();
        }
    }

    public function finishGame()
    {
        $text = "Вы выполнили все задания." . PHP_EOL .
            "Поздравляем!";
        $this->setText($text);

        $url = Storage::disk('point')->url('firework.JPG');
        $this->setImg($url);

        foreach ($this->users as $user){
            if(!$user['confirmed'])
                continue;

            $this->setChatId($user['chat_id']);
            $this->send();
        }
    }
}
