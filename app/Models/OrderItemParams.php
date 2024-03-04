<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemParams extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'param_id',
        'value',
        'order_id'
    ];

    public function Order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function OrderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function ProductParam()
    {
        return $this->belongsTo(ProductParma::class, 'param_id');
    }
}
