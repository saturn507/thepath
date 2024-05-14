<?php

namespace App\Services\Telegram;

use Illuminate\Http\Request;

class TgDTOService
{
    public static function transformWbhookData(Request $request)
    {
        return [
            'chat_id' => $request->input('message.chat.id'),
            'is_bot' => $request->input('message.from.is_bot'),
            'first_name' => $request->input('message.from.first_name'),
            'last_name' => $request->input('message.from.last_name'),
            'username' => $request->input('message.from.username'),
            'language_code' => $request->input('message.from.language_code'),
            'command' =>
                $request->input('message.entities.0.type') == 'bot_command'
                    ?$request->input('message.text')
                    :null,
            'text' => $request->input('message.text'),
        ];
    }
}
