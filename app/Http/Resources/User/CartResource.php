<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseResource;

class CartResource extends BaseResource
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
            'product' => ProductResource::create($this->product),
            'quantity' => $this->quantity,
            'options' => $this->options($this)

        ];
    }

    public function options($obj)
    {
        $items = $obj->CartItem()->get();
        $res = [];
        foreach ($items as $item) {
            $param = $item->ProductParam;
            if ($param) {
                $res[] = [
                    'id' => $item->id,
                    'value' => $param->value,
                ];
            }
        }
        return $res;
    }
}
