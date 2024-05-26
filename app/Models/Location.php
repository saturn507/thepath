<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';

    protected $fillable = [
        'act',
        'city_id',
        'name',
        'address',
        'lat',
        'lng',
        'historical_reference'
    ];

    public function points()
    {
        return $this->hasMany(Point::class, 'id', 'location_id');
    }
}
