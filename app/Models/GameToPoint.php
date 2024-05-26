<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameToPoint extends Model
{
    use HasFactory;

    protected $table = 'game_to_points';

    protected $fillable = [
        'game_id',
        'point_id',
        'completed',
        'answer',
        'created_at',
        'updated_at'
    ];
}
