<?php

namespace App\Http\Resources\Admin;

use App\Helper\_Core;
use App\Http\Resources\BaseResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

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
            'tracking_number' => $this->tracking_number,
            'quantity' => $this->getQuantity($this),
            'status' => $this['status'],
            'user' => $this->user($this),
            'user_address' => $this->userAddress($this),
            'items' => $this->orderItems($this),
            'total_amount' => $this->total_amount,
            'created_at' => Carbon::parse($this->created_at)->format('j F Y'),

        ];
    }

    private function user($order)
    {
        $name = Route::currentRouteName(); // string
        $user = $order->user;
        if ($name == "order.index") {
            return [
                'id' => $order->user_id,
                'full_name' => $user->first_name . ' ' . $user->last_name,
            ];
        } else {
            return [
                'id' => $order->user_id,
                'full_name' => $user->first_name . ' ' . $user->last_name,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'country' => $user->country,
                'country_code' => $user->country_code,
                'phone_number' => $user->phone_number,
            ];
        }
    }

    private function userAddress($order)
    {
        $name = Route::currentRouteName(); // string
        $user_address = $order->userAddress;
        if ($name == "order.index") {
            return [
                'id' => $order->user_address_id,
                'address' => $user_address->country . ", " . $user_address->city . ", " . $user_address->building . ", " . $user_address->flat_number,
            ];
        } else {
            return [
                'id' => $order->user_address_id,
                'address' => $user_address->address,
                'country' => $user_address->country,
                'city' => $user_address->city,
                'building' => $user_address->building,
                'flat_number' => $user_address->flat_number,
                'map_url' => $user_address->map_url,
                'type' => $user_address->type,
                'is_default' => $user_address->is_default,
                'user_ip' => $user_address->user_ip,
                'full_address' => $user_address->country . ", " . $user_address->city . ", " . $user_address->building . ", " . $user_address->flat_number
            ];
        }
    }

    private function orderItems($order)
    {
        $name = Route::currentRouteName(); // string
        if ($name != "order.index") {

            $items = $order->OrderItems;
            $res = [];
            foreach ($items as $item) {
                $params = $item->OrderItemParam()->get();
                $list = [];
                foreach ($params as $param) {
                    $list[] = [
                        'id' => $param->id,
                        'value' => $param->value,
                        'type' => $param->ProductParam->ParamType->param
                    ];
                }
                $res[] =
                    [
                        'id' => $item->id,
                        'product' => [
                            "id" => $item->Product->id,
                            "name" => $item->Product->name,
                        ],
                        'params' => $list,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
            }
            return $res;
        }
        return [];
    }

    public function getQuantity($order)
    {
        return $order->OrderItems->sum('quantity');
    }
}
