<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestLineToPoint extends Model
{
    use HasFactory;

    protected $table = 'quest_line_to_points';

    protected $fillable = [
        'quest_line_id',
        'point_id'
    ];
}
