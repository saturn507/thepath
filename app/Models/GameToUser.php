<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GameToUser extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;
    protected $fillable = [
        'game_id',
        'user_id',
        'capitan',
        'confirmed'
    ];

    protected $table = 'game_to_users';

    public function users(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
