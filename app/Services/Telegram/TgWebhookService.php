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
    private static ?array $tgDTO;
    public function __construct()
    {
        self::$tgDTO = TgDTOService::$tgData;
    }

    public function run(): void
    {
        $this->checkUser();

        $game = GameService::checkCurrentGameFromUser();
        if($game){
            $cacheKey = GameModel::CACHE_GAME_STATE . $game->id;

            if (Cache::has($cacheKey)) {
                self::$tgDTO['callback'] = Cache::get($cacheKey);
            }
        }

        if(!is_null(self::$tgDTO['command'])){
            (new TgCommand(TgDTOService::$tgData))->run();
            return;
        }

        if(!is_null(self::$tgDTO['callback'])){
            (new TgCallback(TgDTOService::$tgData))->run();
            return;
        }

        (new NewGameCommand(self::$tgDTO))->answer();

    }

    private function checkUser()
    {
        $tgUser = TgUser::with('user')->where('chat_id', self::$tgDTO['chat_id'])
            ->first();

        if($tgUser)
            return $this->userUpdate($tgUser);

        if(self::$tgDTO['is_bot'])
            return;

        $user = User::create([
            'name' => trim(self::$tgDTO['first_name'] . " " . self::$tgDTO['last_name']),
            'email' => self::$tgDTO['username'] . "@tg.ru",
            'password' =>Hash::make(self::$tgDTO['username'])
        ]);

        TgDTOService::$tgData['user_id'] = $tgUser->user_id;

        $tgUser = TgUser::create([
            'user_id' => $user->id,
            'chat_id' => self::$tgDTO['chat_id'],
            'is_bot' => self::$tgDTO['is_bot'],
            'first_name' => self::$tgDTO['first_name'],
            'last_name' => self::$tgDTO['last_name'],
            'username' => self::$tgDTO['username'],
            'language_code' => self::$tgDTO['language_code']
        ]);

        return $tgUser;
    }

    private function userUpdate($tgUser)
    {
        TgDTOService::$tgData['user_id'] = $tgUser->user_id;

        $lastUpdate = Carbon::parse($tgUser->updated_at)->timestamp;
        $now = Carbon::now()->timestamp;

        if(($now - $lastUpdate) > 60 * 60 * 24 * 2 || !$tgUser->act){
            $tgUser->first_name = self::$tgDTO['first_name'];
            $tgUser->last_name = self::$tgDTO['last_name'];
            $tgUser->username = self::$tgDTO['username'];
            $tgUser->language_code = self::$tgDTO['language_code'];
            $tgUser->act = true;
            $tgUser->save();
        }

        return $tgUser;
    }
}
