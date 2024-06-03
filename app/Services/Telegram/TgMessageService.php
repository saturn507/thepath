<?php

namespace App\Services\Telegram;

use App\Models\Telegram\TgUser;
use Illuminate\Support\Facades\Http;

trait TgMessageService
{
    private string $text = '';
    private ?string $img = null;
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

        if(!is_null($this->img)){
            $data = [
                'chat_id' => $this->data['chat_id'],
                'caption' => $this->getText(),
                'photo' => $this->img
            ];
            $this->push('sendPhoto', $data);
        } else {
            $this->push('sendMessage', $data);
        }
    }

    public function delete()
    {
        $data = [
            'chat_id' => $this->data['chat_id'],
            'message_id' => $this->data['message_id'],
        ];

        $this->push('deleteMessage', $data);
    }

    private function push($method, $data)
    {
        $res = Http::post(
            'https://api.telegram.org/bot7178639784:AAGbpIsLVJqVQMGdE3Bd0oO6UrDhj-2vYyk/' . $method,
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

    public function setImg($img): void
    {
        $this->img = $img;
    }

    private function getImg(): string
    {
        return $this->img;
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
