<?php

use App\Http\Controllers\Telegram\TgWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function () {
    echo "api ok";
});

Route::post('tg-webhook/de7231e20486abbaa75ed0b2600b9085',[TgWebhookController::class, 'getWebhook']);
