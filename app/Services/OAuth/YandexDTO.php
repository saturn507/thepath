<?php

namespace App\Services\OAuth;

class YandexDTO
{
    private static $social = 'yandex';

    public static function transformData(?object $data): ?array
    {
        return [
            'name' => $data->nickname,
            'email' => $data->email,
            'profile' => [
                'email' => $data->email,
                'social' => self::$social,
                'social_id' => (int) $data->id,
                'first_name' => $data->user['first_name'] ?? null,
                'last_name' => $data->user['last_name'] ?? null,
                'sex' => $data->user['sex'] ?? null,
                'birthday' => $data->user['birthday'] ?? null,
                'avatar' => $data->avatar ?? null,
            ],
        ];
    }
}
