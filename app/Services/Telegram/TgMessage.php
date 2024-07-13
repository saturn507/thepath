<?php

namespace App\Services\Telegram;

use App\Services\Telegram\TgSender;
use Illuminate\Support\Facades\Storage;
use App\Services\Game\Game;

class TgMessage
{
    use TgSender;
    public function currentPoint($data)
    {
        //TgDTOService::$tgData;

        $users = Game::getGameUsers();

        $text = "Вам нужно быть здесь: " . PHP_EOL .
            $data['location'] . PHP_EOL .
            "Ответьте на вопрос:" . PHP_EOL . $data['question'];

        if (!is_null($data['question_img'])) {
            $url = Storage::disk('point')->url($data['question_img']);
            $this->setImg($url);
        }

        $this->setText($text);

        foreach ($users as $user){
            if(!$user['confirmed'])
                continue;

            $this->setChatId($user['chat_id']);
            $this->send();
        }
    }
}
