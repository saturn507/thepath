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

    public function listGame(): void
    {
        $this->pagination();

        $newGame = new NewGame();
        $obj = $newGame->list($this->data);

        if(isset($obj['new']))
            $this->newGame($obj['new']);

    }

    private function newGame($obj): void
    {
        $this->setText('Выбирите квест:');

        $arr = [];
        foreach($obj as $value){
            $arr[] = [
                'text' => '#'. $value->id . ' ' .$value->name,
                'callback_data' => 'description_game.' . $value->hash,
            ];
        }

        $this->createButton(array_chunk($arr, 2));

        /*$this->pushButton([
            [
                'text' => 'Показать еще варианты',
                'callback_data' => 'list_game.' . $this->data['pagination']['next_page'],
            ]
        ]);*/

        $this->pushButton($this->paginationButton());

        $this->send();
    }

    private function pagination()
    {
        $this->data['pagination']['count'] = 2;

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

}
