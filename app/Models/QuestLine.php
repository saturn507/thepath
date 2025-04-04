<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestLine extends Model
{
    use HasFactory;

    protected $table = 'quest_lines';

    protected $fillable = [
        'act',
        'name',
        'description',
        'hash'
    ];
}
