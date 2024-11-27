<?php

namespace App\Services\OAuth;

class VkontakteDTO
{
    private static $social = 'vkontakte';
    public static function transformData(?object $data): ?array
    {
        $nickname = $data->nickname ?? $data->id;

        return [
            'name' => $nickname,
            'email' => $data->id . '@' . self::$social . '.ru',
            'profile' => [
                'email' => $data->email ?? null,
                'social' => self::$social,
                'social_id' => $data->id,
                'first_name' => $data->user['first_name'] ?? null,
                'last_name' => $data->user['last_name'] ?? null,
                'sex' => $data->user['sex'] ?? null,
                'birthday' => $data->user['birthday'] ?? null,
                'avatar' => $data->avatar ?? null,
            ],
        ];
    }
}
