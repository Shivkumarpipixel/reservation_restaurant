<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Availability;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'image',
        'password',
        'address',
        'phone_number'
    ];

    public function availability()
    {
        return $this->hasMany(Availability::class, 'restaurant_id');
    }
}
