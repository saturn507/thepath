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
                'message.from.first_name',
                $request->input('callback_query.message.from.first_name')
            ),
            'last_name' => $request->input(
                'message.from.last_name',
                $request->input('callback_query.message.from.last_name')
            ),
            'username' => $request->input(
                'message.from.username',
                $request->input('callback_query.message.from.username')
            ),
            'language_code' => $request->input(
                'message.from.language_code',
                $request->input('callback_query.message.from.language_code')
            ),
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
