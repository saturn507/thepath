<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestLine extends Model
{
    use HasFactory;

    protected $table = 'quest_lines';

    protected $fillable = [
        'act',
        'name',
        'hash'
    ];
}
