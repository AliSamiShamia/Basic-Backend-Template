<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wishlist extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id'
    ];

    public function Product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
