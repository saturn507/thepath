<?php

namespace App\Services\Telegram;

use App\Models\Telegram\TgUser;
use Illuminate\Support\Facades\Http;

trait TgMessageService
{
    private string $text = '';
    private array $button = [];

    public function send(): void
    {
        $data = [
            'chat_id' => $this->data['chat_id'],
            'text' => $this->getText(),
        ];

        if(count($this->button) > 0) {
            $data['reply_markup'] = json_encode([
                'inline_keyboard' => $this->button,
                'resize_keyboard' => TRUE,
                'one_time_keyboard' => TRUE
            ]);
        }
        //dd($data['reply_markup']);
        $res = Http::post(
            'https://api.telegram.org/bot7178639784:AAGbpIsLVJqVQMGdE3Bd0oO6UrDhj-2vYyk/sendMessage',
                $data
        );

        //dd($res->body());

        if($res->status() == 403){
            TgUser::where('chat_id', $this->webhookData['chat_id'])->update(['act' => false]);
        }
    }

    public function setText($text): void
    {
        $this->text .= $text;
    }

    private function getText(): string
    {
        return $this->text;
    }

    public function createButton($array)
    {
        $this->button = $array;
    }

    public function pushButton($array)
    {
        array_push($this->button, $array);
    }
}
