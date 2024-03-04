<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseResource;
use Carbon\Carbon;

class OrderResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'tracking_number' => strtoupper($this->tracking_number),
            'status' => strtoupper($this['status']),
            'user' => $this->User->first_name . " " . $this->User->middle_name. " " . $this->User->last_name,
            'user_address' => $this->userAddress($this),
            'items' => $this->orderItems($this),
            'quantity' => $this->getQuantity($this),
            'total_amount' => $this->total_amount,
            'created_at' => Carbon::parse($this->created_at)->format('j F Y'),
        ];
    }

    private function userAddress($order)
    {
        $user_address = $order->userAddress;
        return [
            'id' => $order->user_address_id,
            'country' => $user_address->country,
            'country_code' => $user_address->country,
            'address' => $user_address->country,
            'city' => $user_address->city,
            'building' => $user_address->building,
            'flat_number' => $user_address->flat_number,
            'map_url' => $user_address->map_url,
            'type' => $user_address->type,
            'is_default' => $user_address->is_default,
        ];
    }

    private function orderItems($order)
    {
        $items = $order->OrderItems;
        $res = [];
        foreach ($items as $item) {
            $dicount_val = 0;
            if ($item->discount) {
                $discount = [
                    'id' => $item->discount_id,
                    'name' => $item->discount->name,
                    'decription' => $item->discount->name,
                    'percentage' => $item->discount->discount_percent,
                ];
                $dicount_val = $item->dicount_percent;
            }
            $res[] =
                [
                    'id' => $item->id,
                    'product' => ProductResource::create($item->product),
                    'discount' => $discount ?? [],
                    'quantity' => $item->quantity,
                    'price' => $item->price * ((100 - $dicount_val) / 100),
                ];
        }
        return $res;
    }

    public function getQuantity($order)
    {
        return $order->OrderItems->sum('quantity');
    }
}
