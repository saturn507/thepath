<?php

namespace App\Services\Telegram\Command;

use App\Services\Game\NewGame as NG;
use App\Services\Telegram\TgMessageService;

class NewGame
{
    use TgMessageService;

    private array $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function run(): void
    {
        $newGame = new NG();
        $obj = $newGame->list($this->data['user_id']);

        if(isset($obj['new']))
            $this->newGame($obj['new']);

        //dd($obj);
        return;
    }

    private function newGame($obj): void
    {
        $this->setText('Выбирите квест: ');

        foreach($obj as $value){

            $arr = [
                [
                    'text' => $value->name,
                    'callback_data' => 'create_game.' . $value->hash,
                ],
                [
                    'text' => 'Описание ->',
                    'callback_data' => 'description_game.' . $value->hash,
                ]
            ];

            $this->createButton($arr);
        }

        $this->send();
    }
}
