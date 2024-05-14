<?php

namespace App\Services\Telegram;

use App\Models\Telegram\TgUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class TgWebhookService
{
    private array $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function run()
    {
        $user = $this->checkUser();
        dd($user);
    }

    private function checkUser()
    {
        $tgUser = TgUser::where('chat_id', $this->data['chat_id'])
            ->first();

        if($tgUser)
            return $tgUser;

        $user = User::create([
            'name' => trim($this->data['first_name'] . " " . $this->data['last_name']),
            'email' => $this->data['username'] . "@tg.ru",
            'password' =>Hash::make($this->data['username'])
        ]);

        $tgUser = TgUser::create([
            'user_id' => $user->id,
            'chat_id' => $this->data['chat_id'],
            'is_bot' => $this->data['is_bot'],
            'first_name' => $this->data['first_name'],
            'last_name' => $this->data['last_name'],
            'username' => $this->data['username'],
            'language_code' => $this->data['language_code']
        ]);
    }
}
