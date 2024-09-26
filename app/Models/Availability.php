<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Restaurant;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'day',
        'meal_type',
        'time_slot',
        'open',
        'available_seats',
        'opening_time',
        'closing_time'
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
}
