<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
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
            'is_default' => $this->is_default?"Default":"-",
            'created_at' => Carbon::parse($this->created_at)->format('j F Y'),
        ];
    }
}
