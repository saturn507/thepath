<?php

namespace App\Services\Telegram\Command;

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

    public function run(): void
    {
        $newGame = new NewGame();
        $obj = $newGame->list($this->data['user_id']);

        if(isset($obj['new']))
            $this->newGame($obj['new']);

        //dd($obj);
        return;
    }

    private function newGame($obj): void
    {
        $this->setText('Выбирите квест: ');

        $arr = [];
        foreach($obj as $value){
            $arr[] = [
                'text' => '#'. $value->id . ' ' .$value->name,
                'callback_data' => 'description_game.' . $value->hash,
            ];
        }

        $this->createButton(array_chunk($arr, 2));

        $this->send();
    }
}
