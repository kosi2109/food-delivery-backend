<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'quantity',
        'portion_id',
        'price',
        'order_id',
        'total',
        'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function portion()
    {
        return $this->belongsTo(Portion::class);
    }

    public function getFilteredOrderItems()
{
    return $this->orderItems()->whereHas('item', function ($query) {
        $query->where('created_by', Auth::id());
    })->get();
}
}
