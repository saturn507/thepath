<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Point extends Model
{
    use HasFactory;

    protected $table = 'points';

    protected $fillable = [
        'act',
        'location_id',
        'question',
        'answer'
    ];

    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }
}
