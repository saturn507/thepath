<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TgWebhookController extends Controller
{
    public function getWebhook(Request $request)
    {
        if($request->has('message')){

            Log::channel('telegram')->alert(json_encode($request->input('message')));
            echo "ok";
        }
    }
}
