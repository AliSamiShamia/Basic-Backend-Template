<?php

namespace App\Http\Resources\Admin;

use App\Helper\_MediaHelper;
use App\Http\Resources\BaseResource;
use App\Helper\_Core;

class ProductResource extends BaseResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => [
                'label' => $this->sku,
                'message' => "default",
            ],
            'description' => $this->description,
            'brief' => $this->brief,
            'price' => $this->price,
            'pre_price' => $this->pre_price,
            'weight' => $this->weight,
            'stock' => $this->stock,
            'stock_column' => [
                'label' => $this->stock,
                'message' => $this->stock > 10 ? "info" : "warning",
            ],
            'is_trending' => (bool) $this->is_trending,
            'is_live' => (bool)$this->is_live,
            'is_featured' =>  (bool)$this->is_featured,
            'discount_id ' => $this->getDiscount($this),
            'media' => $this->media($this),
            'categories' => $this->categories($this),
            'params' => _Core::getParamType($this),
        ];
    }


    public function media($obj)
    {
        $media = $obj->allMedia()->get();
        $data = [];
        foreach ($media as $item) {
            $data[] = [
                'id' => $item->id,
                'url' => _MediaHelper::getURL($item->url, $item->type),
            ];
        }
        return $data;
    }

    public function getDiscount($obj)
    {
        $discount = $obj->Discount()->first();
        if ($discount) {
            return [
                'id' => $discount->id,
                'name' => $discount->name,
                'description' => $discount->description,
                'discount_percent' => $discount->discount_percent,
                'active' => $discount->active
            ];
        }
        return null;
    }

    public function categories($obj)
    {
        $cats = $obj->Categories;
        if ($cats) {
            $res = [];
            foreach ($cats as $cat) {
                $res[] = [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'parent_id' => $cat->parent_id,
                ];
            }
            return $res;
        }
        return null;
    }
}
