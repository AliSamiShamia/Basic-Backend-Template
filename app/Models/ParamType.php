<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParamType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'param'
    ];

    public function ProductParma()
    {
        return $this->hasMany(ProductParma::class, 'type_id');
    }
}
