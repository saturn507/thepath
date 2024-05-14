<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\Telegram\TgLog;
use App\Services\Telegram\TgDTOService;
use App\Services\Telegram\TgWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TgWebhookController extends Controller
{
    public function getWebhook(Request $request)
    {
        if($request->has('message')){
            $this->webhookLog($request);

            $res = TgDTOService::transformWbhookData($request);
            (new TgWebhookService(TgDTOService::transformWbhookData($request)))->run();
            dd($res);

            Log::channel('telegram')->alert(json_encode($request->input('message')));
            echo "ok";
        }
    }

    private function webhookLog($request): void
    {
        TgLog::create([
            'chat_id' => $request->input('message.chat.id'),
            'text' => $request->input('message.text'),
            'log' => json_encode($request->input('message')),
        ]);
    }
}
