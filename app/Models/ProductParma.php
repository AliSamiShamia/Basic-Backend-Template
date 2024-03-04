<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductParma extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type_id', 'value', 'product_id'
    ];


    public function Product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }


    public function ParamType()
    {
        return $this->belongsTo(ParamType::class, 'type_id');
    }
}
