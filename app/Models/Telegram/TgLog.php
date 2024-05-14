<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'text',
        'log'
    ];
}
