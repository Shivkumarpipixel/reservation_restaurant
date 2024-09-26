<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClosedDate extends Model
{
    use HasFactory;
   
    protected $fillable = [
        'restaurant_id',
        'weekday'
    ];

}
