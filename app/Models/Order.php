<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_address',
        'latitude',
        'longitude',
        'customer_id',
        'delivery_note',
        'delivery_cost',
        'payment_type_id',
        'grand_total',
        'status'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getStatusTextAttribute()
    {
        return config('status.status')[$this->status] ?? 'Unknown';
    }

    public function getPaymentTypeTextAttribute()
    {
        return config('payment_types.types')[$this->payment_type_id] ?? 'Unknown';
    }
}
