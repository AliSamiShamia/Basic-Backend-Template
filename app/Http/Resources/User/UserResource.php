<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseResource;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class UserResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
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
            'phone_number' => $this->phone_number,
            'verified_at' => Carbon::parse($this->phone_verified_at)->format('j F Y'),
            'created_at' => Carbon::parse($this->created_at)->format('j F Y'),
            'token' => $this->createToken('API Token')->accessToken,
            'complete_info' => $this->checkInfo($this),
            'has_permission' => $this->has_permission,

        ];
    }

    public function checkInfo($user)
    {
        $address = $user->addresses()->first();
        if (!$user->first_name || !$user->last_name || !$user->phone_number || !$user->country_code || !$user->email || !$address) {
            return false;
        }
        return true;
    }
}

