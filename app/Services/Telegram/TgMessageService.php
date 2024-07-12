<?php

namespace App\Services\Telegram;

use App\Models\Telegram\TgUser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait TgMessageService
{
    private string $text = '';
    private ?string $img = null;
    private array $button = [];

    public function send(): void
    {
        $data['chat_id'] = TgDTOService::$tgData['chat_id'];

        if(count($this->button) > 0) {
            $data['reply_markup'] = json_encode([
                'inline_keyboard' => $this->button,
                'resize_keyboard' => TRUE,
                'one_time_keyboard' => TRUE
            ]);
        }

        if(!is_null($this->img)){
            $data['caption'] = $this->getText();
            $data['photo'] = $this->img;

            $this->push('sendPhoto', $data);
        } else {
            $data['text'] = $this->getText();
            $this->push('sendMessage', $data);
        }
    }

    public function delete()
    {
        $data = [
            'chat_id' => TgDTOService::$tgData['chat_id'],
            'message_id' => TgDTOService::$tgData['message_id'],
        ];

        $this->push('deleteMessage', $data);
    }

    private function push($method, $data)
    {
        $res = Http::post(
            'https://api.telegram.org/bot' . config('telegram.token') . '/' . $method,
            $data
        );

        Log::alert($res->body());
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

    public function resetText()
    {
        $this->text = '';
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
