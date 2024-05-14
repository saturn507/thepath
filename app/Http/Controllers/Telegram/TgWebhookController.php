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

            (new TgWebhookService(TgDTOService::transformWbhookData($request)))->run();

            Log::channel('telegram')->alert(json_encode($request->input('message')));

            echo "ok";
        }

        Log::channel('telegram')->alert('not message');
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
