<?php

namespace App\Services\Telegram\Command;

use App\Services\Game\Game as GameService;
use App\Services\Telegram\TgMessageService;

class MyTeamCommand
{
    use TgMessageService;

    private array $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function myTeamList()
    {
        $game = GameService::checkCurrentGameFromUser($this->data['user_id']);

        if($game){
            $text = 'Ваша команда:' . PHP_EOL . PHP_EOL;
            $users = GameService::getGameUsers();

            foreach($users as $user){
                $capitan = ($user['capitan'] === true) ? '(C)' : '';
                $confirmed = ($user['confirmed'] === true) ? '*' : '';
                $text .= $user['first_name'] . ' ' . $user['last_name'] . ' ' .
                    $user['username'] . ' ' . $capitan . ' ' . $confirmed . PHP_EOL;
            }

            $text .= PHP_EOL . '(C) - капитан' . PHP_EOL . '* - участие подтверждено';

            $arr = [
                [
                    'text' => 'Пригласить',
                    'callback_data' => 'my_team_user_add',
                ],
                [
                    'text' => 'Удалить',
                    'callback_data' => 'my_team_user_delete',
                ],
                /*[
                    'text' => 'Сменить капитана',
                    'callback_data' => 'my_team_user_capitan_change',
                ],*/
                [
                    'text' => '[X] Закрыть',
                    'callback_data' => 'delete_callback',
                ],
            ];

            $this->createButton(array_chunk($arr, 2));

            $this->setText($text);
            $this->send();

        } else {
            $this->setText('У Вас нет созданых игр');
            $this->send();
        }
    }
}
