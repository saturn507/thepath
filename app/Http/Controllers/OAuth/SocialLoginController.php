<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{

    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        $userSource = Socialite::driver($provider)->user();
        dd($userSource);
        if($userSource){
            $user = match ($provider){
                'yandex' => 1,
                'vkontakte' => 1,
                default => false
            };
        }
    }


}
