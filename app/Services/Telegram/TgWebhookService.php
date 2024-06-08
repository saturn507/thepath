<?php

namespace App\Services\Telegram;

use App\Models\Telegram\TgUser;
use App\Models\User;
use App\Services\Game\Game as GameService;
use App\Services\Game\NewGame;
use App\Services\Telegram\Callback\GameCallback;
use App\Services\Telegram\Command\NewGameCommand;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class TgWebhookService
{
    private array $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function run(): void
    {
        $this->checkUser();

        $game = GameService::checkCurrentGameFromUser();
        if($game){
            $cacheKey = 'game_state_' . $game->id;

            if (Cache::has($cacheKey)) {
                $this->data['callback'] = Cache::get($cacheKey);
            }
        }

        if(!is_null($this->data['command'])){
            (new TgCommand($this->data))->run();
            return;
        }

        if(!is_null($this->data['callback'])){
            (new TgCallback($this->data))->run();
            return;
        }

        (new NewGameCommand($this->data))->answer();

    }

    private function checkUser()
    {
        $tgUser = TgUser::with('user')->where('chat_id', $this->data['chat_id'])
            ->first();

        if($tgUser)
            return $this->userUpdate($tgUser);

        if($this->data['is_bot'])
            return;

        $user = User::create([
            'name' => trim($this->data['first_name'] . " " . $this->data['last_name']),
            'email' => $this->data['username'] . "@tg.ru",
            'password' =>Hash::make($this->data['username'])
        ]);

        $this->data['user_id'] = $tgUser->user_id;
        TgDTOService::$tgData['user_id'] = $tgUser->user_id;

        $tgUser = TgUser::create([
            'user_id' => $user->id,
            'chat_id' => $this->data['chat_id'],
            'is_bot' => $this->data['is_bot'],
            'first_name' => $this->data['first_name'],
            'last_name' => $this->data['last_name'],
            'username' => $this->data['username'],
            'language_code' => $this->data['language_code']
        ]);

        return $tgUser;
    }

    private function userUpdate($tgUser)
    {
        $this->data['user_id'] = $tgUser->user_id;
        TgDTOService::$tgData['user_id'] = $tgUser->user_id;

        $lastUpdate = Carbon::parse($tgUser->updated_at)->timestamp;
        $now = Carbon::now()->timestamp;

        if(($now - $lastUpdate) > 60 * 60 * 24 * 2 || !$tgUser->act){
            $tgUser->first_name = $this->data['first_name'];
            $tgUser->last_name = $this->data['last_name'];
            $tgUser->username = $this->data['username'];
            $tgUser->language_code = $this->data['language_code'];
            $tgUser->act = true;
            $tgUser->save();
        }

        return $tgUser;
    }
}
