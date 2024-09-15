<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portion extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'size',
        'price',
        'created_by'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
