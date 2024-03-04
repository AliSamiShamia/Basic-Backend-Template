<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id', 'param_id'];

    public function Cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    public function ProductParam()
    {
        return $this->belongsTo(ProductParma::class, 'param_id');
    }
}
