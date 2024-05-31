<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PointToAnswer extends Model
{
    use HasFactory;

    protected $table = 'point_to_answers';

    protected $fillable = [
        'point_id',
        'answer',
        'answer_transformation'
    ];
}
