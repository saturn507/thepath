<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OAuth\VkontakteDTO;
use App\Services\OAuth\YandexDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Auth;

class SocialLoginController extends Controller
{

    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        $userSource = Socialite::driver($provider)->user();

        $data = match ($provider){
            'yandex' => YandexDTO::transformData($userSource),
            'vkontakte' => VkontakteDTO::transformData($userSource),
            default => false
        };

        if(!$data)
            return;

        $user = User::query()->where('email', $data['email'])->first();

        if(!$user){
            $data['password'] = Hash::make(json_encode($data));

            $user = User::query()
                ->create($data);

            $user->profile()->create($data['profile']);
        }

        Auth::login($user);

        dd($user->createToken('authToken')->accessToken);
    }


}
