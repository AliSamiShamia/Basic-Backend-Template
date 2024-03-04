<?php

namespace App\Models;

use App\Helper\_SKUHelper;
use App\Traits\Mediable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, Mediable;


    protected $fillable = ['name', 'sku', 'description', 'brief', 'price', 'pre_price', 'weight', 'stock', 'is_trending', 'is_live', 'is_featured', 'discount_id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'sku', 'price'])
            ->setDescriptionForEvent(fn(string $eventName) => "Product {$eventName}")
            ->useLogName('product log')
            ->dontLogIfAttributesChangedOnly(['brief', 'weight'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $slug = Str::slug($model->name);
            $model->sku = $slug ;
            $model->slug = $slug;
            if ($model->pre_price == null) {
                $model->pre_price = $model->price;
            }
        });

        self::created(function ($model) {
            $model->sku = _SKUHelper::generateSKU($model->name, $model->id);
        });
    }

    public function Discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }

    public function Wishlist()
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }

    public function Categories()
    {
        return $this->belongsToMany(Category::class, 'category_products');
    }

    public function ProductParams()
    {
        return $this->hasMany(ProductParma::class, 'product_id');
    }
}
