<?php

namespace App\Traits;

use App\Models\Order;
use App\Models\UserAddress;

trait UserTrait
{
    public function addresses()
    {
        return $this->hasMany(UserAddress::class, 'user_id')->orderByDesc('created_at');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id')->where([
            ['status', '!=', 'initialized']
        ])->orderByDesc('created_at');
    }

    public function ordersCount()
    {
        return $this->hasMany(Order::class, 'user_id')->where([
            ['status', '!=', 'initialized']
        ])->count();
    }

    public function ordersTotalAmount()
    {
        return $this->hasMany(Order::class, 'user_id')->where([
            ['status', '!=', 'initialized']
        ])->sum('total_amount');
    }
}
