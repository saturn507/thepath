<?php

namespace App\Services\Telegram\Callback;

use App\Models\GameToUser;
use App\Models\Telegram\TgUser;
use App\Services\Game\Game as GameService;
use App\Services\Telegram\TgMessageService;
use Illuminate\Support\Facades\Cache;

class MyTeamCallback
{
    use TgMessageService;

    private array $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function userTeamAddEnter()
    {
        $game = GameService::checkCurrentGameFromUser();
        $cacheKey = 'game_state_' . $game->id;
        $state = 'my_team_user_add';

        Cache::put($cacheKey, $state, 60*60);

        $text = 'Отправте username пользователя telegram которого хотите добавить в команду.' . PHP_EOL;
        $text .= 'Пользователь должен быть подписан на телеграм бота @the_path_bot';
        $this->setText($text);
        $this->send();
    }

    public function userTeamAdd()
    {
        $userName = str_replace('@', '', $this->data['text']);

        $tgUser = TgUser::where('username', $userName)->first();

        if($tgUser){
            $currentUsers = GameService::getGameUsers();
            if(isset($currentUsers[$tgUser->user_id])){
                $text = 'Этот пользовател уже в вашей команде';
                $this->setText($text);
                $this->send();
            } else {
                $game = GameService::checkCurrentGameFromUser();
                GameToUser::create([
                    'game_id' => $game->id,
                    'user_id' => $tgUser->user_id
                ]);

                Cache::forget('users_game_' . $game->id);

                $text = 'Пользователю отправлено приглашение';
                $this->setText($text);
                $this->send();

                $text = 'Вас пригласили в команду';
                $this->setChatId($tgUser->chat_id);
                $this->setText($text);
                $this->send();
            }
        } else {
            $text = 'Пользователь ' . $this->data['text'] . ' не найден';
            $this->setText($text);
            $this->send();
        }
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
                        'text' =>  $user['first_name'] . ' ' . $user['last_name'] . ' ' . $user['username'],
                        'callback_data' => 'my_team_user_delete.' . $k,
                    ];
                }
            }

            if(count($arr) > 0){
                $this->setText('Кого?');
                $this->createButton(array_chunk($arr, 2));
                $this->send();
            } else {
                $this->setText('Нет пользователейдля удаления, капитана удалить нельзя.');
                $this->send();
            }
        }
    }
}
