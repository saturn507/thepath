<?php

namespace App\Services\Telegram;

use Illuminate\Http\Request;

class TgDTOService
{
    public static function transformWbhookData(Request $request)
    {

        return [
            'chat_id' => $request->input(
                'message.chat.id',
                $request->input('callback_query.message.chat.id')
            ),
            'is_bot' => $request->input(
                'message.from.is_bot',
                $request->input('callback_query.message.from.is_bot')
            ),
            'first_name' => $request->input(
                'message.chat.first_name',
                $request->input('callback_query.message.chat.first_name')
            ),
            'last_name' => $request->input(
                'message.chat.last_name',
                $request->input('callback_query.message.chat.last_name')
            ),
            'username' => $request->input(
                'message.chat.username',
                $request->input('callback_query.message.chat.username')
            ),
            'language_code' => $request->input('message.from.language_code', null),
            'command' =>
                $request->input('message.entities.0.type') == 'bot_command'
                    ?substr($request->input('message.text'), 1)
                    :null,
            'text' => $request->input(
                'message.text',
                $request->input('callback_query.message.text')
            ),
            'callback' => $request->input('callback_query.data', null),
            'message_id' => $request->input(
                'message.message_id',
                $request->input('callback_query.message.message_id')
            ),
        ];
    }
}
