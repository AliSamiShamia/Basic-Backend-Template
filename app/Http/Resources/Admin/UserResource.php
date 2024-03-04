<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;
use Carbon\Carbon;

class UserResource extends BaseResource
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
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'country' => $this->country,
            'country_code' => $this->country_code,
            'phone_number' => $this->country_code . '' . $this->phone_number,
            'email_verified_at' => Carbon::parse($this->email_verified_at)->format('j F Y'),
            'phone_verified_at' => Carbon::parse($this->phone_verified_at)->format('j F Y'),
            'created_at' => Carbon::parse($this->created_at)->format('j F Y'),
            'token' => $this->createToken('API Token')->accessToken,
            'role' => $this->role == "admin" ? "admin" : "",
            'ordersCount' => $this->ordersCount(),
            'has_permission' => (bool)$this->has_permission,
            'ordersAmount' => $this->ordersTotalAmount(),
        ];
    }

}
