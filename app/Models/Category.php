<?php

namespace App\Models;

use App\Helper\_SKUHelper;
use App\Traits\Mediable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes, Mediable;

    protected $fillable = ['slug', 'name', 'parent_id'];


    public function Parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $slug = Str::slug($model->name);
            $model->slug = $slug;
        });

        self::created(function ($model) {
            $model->sku = _SKUHelper::generateSKU($model->name, $model->id);
        });
    }

    public function Products()
    {
        return $this->belongsToMany(Product::class, 'category_products');
    }
}
