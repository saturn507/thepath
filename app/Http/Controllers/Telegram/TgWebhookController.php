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
         die('ok');
        Log::channel('telegram')->alert(json_encode($request->all()));

        if($request->has('message') || $request->has('callback_query')){

            $data = TgDTOService::transformWbhookData($request);

            $this->webhookLog($request, $data);

            (new TgWebhookService($data))->run();

            Log::channel('telegram')->alert(json_encode($request->all()));
        }

        Log::channel('telegram')->alert('not message ******* ' . json_encode($request->all()));

        echo "ok";
    }

    private function webhookLog($request, $data): void
    {
        TgLog::create([
            'chat_id' => $data['chat_id'],
            'text' => $data['text'],
            'log' => json_encode($request->all()),
        ]);
    }
}
