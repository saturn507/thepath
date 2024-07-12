<?php

namespace App\Services\Telegram;

use Illuminate\Http\Request;

class TgDTOService
{
    public static array $tgData;
    public static function transformWbhookData(Request $request)
    {
        $command = null;
        $callbackData = null;

        if($request->input('message.entities.0.type', null) == 'bot_command'){
            $command = substr($request->input('message.text'), 1);
        } elseif($request->has('callback_query.data')){
            $callback = explode('.', $request->input('callback_query.data'));

            $command = $callback[0];
            $callbackData = $callback;
        }

        $data =  [
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
            'command' => $command,
            'callback_data' => $callbackData,
            'text' => $request->input(
                'message.text',
                $request->input('callback_query.message.text')
            ),
            'message_id' => $request->input(
                'message.message_id',
                $request->input('callback_query.message.message_id')
            ),
        ];

        self::$tgData = $data;

        return $data;
    }
}
