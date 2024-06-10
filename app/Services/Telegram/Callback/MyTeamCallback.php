<?php

namespace App\Services\Telegram\Callback;

use App\Models\Game as GameModel;
use App\Models\GameToUser;
use App\Models\Telegram\TgUser;
use App\Services\Game\Game as GameService;
use App\Services\Telegram\TgDTOService;
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
        $cacheKey = GameModel::CACHE_GAME_STATE . $game->id;
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

                $users = Cache::get(GameModel::CACHE_GAME_USERS . $game->id);

                $users[$tgUser->user_id] = [
                    "capitan" => false,
                    "confirmed" => false,
                    "first_name" => $tgUser->first_name,
                    "last_name" => $tgUser->last_name,
                    "username" => "@" . $tgUser->username,
                ];

                Cache::put(GameModel::CACHE_GAME_USERS . $game->id, $users, 60*60*8);
                Cache::forget(GameModel::CACHE_GAME_STATE . $game->id);


                $text = 'Пользователю отправлено приглашение';
                $this->setText($text);
                $this->send();

                $this->data['chat_id'] = $tgUser->chat_id;
                $text = 'Вас пригласили в команду';
                $this->resetText();
                $this->setText($text);
                $this->send();
            }
        } else {
            $text = 'Пользователь @' . $this->data['text'] . ' не найден';
            $this->setText($text);
            $this->send();
        }
    }

    public function userTeamDelete()
    {
        $game = GameService::checkCurrentGameFromUser();

        if(isset(TgDTOService::$tgData['callback_data'][1])){

            $userId = TgDTOService::$tgData['callback_data'][1];

            GameToUser::query()
                ->where('game_id', $game->id)
                ->where('user_id', $userId)
                ->delete();

            $users = Cache::get(GameModel::CACHE_GAME_USERS . $game->id);
            unset($users[$userId]);
            Cache::put(GameModel::CACHE_GAME_USERS . $game->id, $users, 60*60*8);

            $this->setText('Удален.');
            $this->send();
        } else {
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
                $this->setText('Нет пользователей для удаления, капитана удалить нельзя.');
                $this->send();
            }
        }
    }
}
