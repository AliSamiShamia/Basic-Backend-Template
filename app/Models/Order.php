<?php

namespace App\Models;

use App\Helper\_Core;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = ['user_address_id', 'tracking_number', 'total_amount', 'status', 'user_id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'tracking_number', 'status'])
            ->setDescriptionForEvent(fn(string $eventName) => "Order {$eventName}")
            ->useLogName('order log')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

//                    "tracking_number" => _Core::generateRandomString() . $user->id,
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->tracking_number = uniqid("JW-");
        });

        static::created(function ($model) {
            $num = str_pad($model->id, 6, '0', STR_PAD_LEFT);
            $model->tracking_number = "JW-" . date("Y") . "-" . $num;
            $model->save();
        });

    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function UserAddress()
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }

    public function OrderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
