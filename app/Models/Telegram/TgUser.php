<?php

namespace App\Models\Telegram;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chat_id',
        'is_bot',
        'first_name',
        'last_name',
        'username',
        'language_code'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
