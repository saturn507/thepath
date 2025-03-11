<?php

namespace App\Services\Telegram;

use App\Models\Game as GameModel;
use App\Models\Telegram\TgUser;
use App\Models\User;
use App\Services\Game\Game as GameService;
use App\Services\Game\NewGame;
use App\Services\Telegram\Callback\GameCallback;
use App\Services\Telegram\Command\NewGameCommand;
use App\Services\Telegram\TgDTOService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class TgWebhookService
{
    /*private static ?array $tgDTO;
    public function __construct()
    {
        self::$tgDTO = TgDTOService::$tgData;
    }*/

    public function run(): void
    {
        $this->checkUser();

        $game = GameService::checkCurrentGameFromUser(TgDTOService::$tgData['user_id']);
        if($game){
            $cacheKey = GameModel::CACHE_GAME_STATE . $game->id;

            if (Cache::has($cacheKey) && is_null(TgDTOService::$tgData['command'])) {
                TgDTOService::$tgData['command'] = Cache::get($cacheKey);
            }
        }

        if(!is_null(TgDTOService::$tgData['command'])){
            (new TgCommand())->run();
            return;
        }

        /*if(!is_null(self::$tgDTO['callback'])){
            (new TgCallback(TgDTOService::$tgData))->run();
            return;
        }*/

        (new NewGameCommand(TgDTOService::$tgData))->answer();

    }

    private function checkUser()
    {
        $tgUser = TgUser::with('user')->where('chat_id', TgDTOService::$tgData['chat_id'])
            ->first();

        if($tgUser)
            return $this->userUpdate($tgUser);

        if(TgDTOService::$tgData['is_bot'])
            return;

        $user = User::create([
            'name' => trim(TgDTOService::$tgData['first_name'] . " " . TgDTOService::$tgData['last_name']),
            'email' => TgDTOService::$tgData['username'] . "@tg.ru",
            'password' =>Hash::make(TgDTOService::$tgData['username'])
        ]);

        TgDTOService::$tgData['user_id'] = $user->user_id;

        $tgUser = TgUser::create([
            'user_id' => $user->id,
            'chat_id' => TgDTOService::$tgData['chat_id'],
            'is_bot' => TgDTOService::$tgData['is_bot'],
            'first_name' => TgDTOService::$tgData['first_name'],
            'last_name' => TgDTOService::$tgData['last_name'],
            'username' => TgDTOService::$tgData['username'],
            'language_code' => TgDTOService::$tgData['language_code']
        ]);

        return $tgUser;
    }

    private function userUpdate($tgUser)
    {
        TgDTOService::$tgData['user_id'] = $tgUser->user_id;

        $lastUpdate = Carbon::parse($tgUser->updated_at)->timestamp;
        $now = Carbon::now()->timestamp;

        if(($now - $lastUpdate) > 60 * 60 * 24 * 2 || !$tgUser->act){
            $tgUser->first_name = TgDTOService::$tgData['first_name'];
            $tgUser->last_name = TgDTOService::$tgData['last_name'];
            $tgUser->username = TgDTOService::$tgData['username'];
            $tgUser->language_code = TgDTOService::$tgData['language_code'];
            $tgUser->act = true;
            $tgUser->save();
        }

        return $tgUser;
    }
}
