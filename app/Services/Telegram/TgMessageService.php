<?php

namespace App\Services\Telegram;

use App\Models\Telegram\TgUser;
use Illuminate\Support\Facades\Http;

class TgMessageService
{
    private TgUser $tgUser;

    private string $text;
    public function __construct($tgUser)
    {
        $this->tgUser = $tgUser;
    }

    public function send(): void
    {
        $res = Http::post(
            'https://api.telegram.org/bot7178639784:AAGbpIsLVJqVQMGdE3Bd0oO6UrDhj-2vYyk/sendMessage',
                [
                    'chat_id' => $this->tgUser->chat_id,
                    'text' => '***' . $this->getText(),
                ]
        );

        if($res->status() == 403){
            TgUser::where('chat_id', $this->webhookData['chat_id'])->update(['act' => false]);
        }
    }

    public function setText($text): void
    {
        $this->text = $text;
    }

    private function getText(): string
    {
        return $this->text;
    }
}
