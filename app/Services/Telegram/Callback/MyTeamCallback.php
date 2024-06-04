<?php

namespace App\Services\Telegram\Callback;

use App\Services\Game\Game as GameService;
use App\Services\Telegram\TgMessageService;

class MyTeamCallback
{
    use TgMessageService;

    private array $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function userTeamDelete()
    {
        if(isset($this->data['callback_data'][1])){
            $this->setText('Удаляю.');
            $this->send();
        } else {
            GameService::checkCurrentGameFromUser($this->data['user_id']);
            $users = GameService::getGameUsers();

            $arr = [];
            foreach($users as $k => $user){
                if(!$user['capitan']){
                    $arr[] = [
                        'text' => 'Удалить',
                        'callback_data' => 'my_team_user_delete.' . $k,
                    ];
                }
            }

            if(count($arr) > 0){
                $this->createButton(array_chunk($arr, 1));
                $this->send();
            } else {
                $this->setText('Нет пользователейдля удаления, капитана удалить нельзя.');
                $this->send();
            }
        }
    }
}
