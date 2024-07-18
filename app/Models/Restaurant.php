<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'shop_type',
        'address',
        'latitude',
        'longitude',
        'rating',
        'is_popular',
        'description',
        'logo',
    ];
}
