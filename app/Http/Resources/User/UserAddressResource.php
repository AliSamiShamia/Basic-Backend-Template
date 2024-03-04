<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseResource;

class UserAddressResource extends BaseResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'country' => $this->country,
            'country_code' => $this->country_code,
            'address' => $this->address,
            'city' => $this->city,
            'building' => $this->building,
            'flat_number' => $this->flat_number,
            'map_url' => $this->map_url,
            'type' => $this->type,
            'is_default' => $this->is_default,
        ];
    }
}
