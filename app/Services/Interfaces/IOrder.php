<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface IOrder extends IBase
{
    public function placeOrder(Request $request);

    public function trackOrder($order_id);


    public function updateStatus($order_id, $status);

    public function orderItems(Request $request, $order_id);
}
