<?php

namespace App\Http\Resources\User;

use App\Helper\_Core;
use App\Helper\_MediaHelper;
use App\Http\Resources\BaseResource;
use App\Models\ParamType;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JsonSerializable;

class ProductResource extends BaseResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'brief' => $this->brief,
            'price' => $this->price,
            'pre_price' => $this->pre_price,
            'weight' => $this->weight,
            'categories' => $this->getCategory($this),
            'stock' => $this->stock,
            'is_trending' => $this->is_trending,
            'is_live' => $this->is_live,
            'is_featured' => $this->is_featured,
            'discount ' => $this->getDiscount($this),
            'media' => $this->media($this),
            'thumbnail' => $this->thumbnail($this),
            'params' => _Core::getParams($this),
            'is_fav' => $this->checkUserFav($this),
        ];
    }

    public function checkUserFav($product)
    {
        $user = Auth::guard('api')->user();
        if ($user) {
            $check = $this->Wishlist()->where([
                'user_id' => $user->id
            ])->first();
            return (bool)$check;
        }
        return false;
    }


    public function getCategory($product)
    {
        $categories = $product->Categories()->get();
        $list = [];
        foreach ($categories as $category) {
            $list[] = [
                'id' => $category->id,
                'slug' => $category->slug,
                'name' => $category->name,
            ];
        }
        return $list;
    }

    public function getDiscount($obj)
    {
        $discount = $obj->Discount()->where([
            'active' => true
        ])->first();
        if ($discount) {
            return [
                'id' => $discount->id,
                'name' => $discount->name,
                'description' => $discount->description,
                'discount_percent' => $discount->discount_percent,
            ];
        }
        return null;
    }

    public function media($obj)
    {
        $images = $obj->images()->orderBy('priority', 'desc')->get();
        $list = [];
        foreach ($images as $item) {
            $list[] = [
                'id' => $item->id,
                'url' => _MediaHelper::getURL($item->url, 'image'),
            ];
        }
        return $list;
    }

    public function thumbnail($obj)
    {
        $thumb = $obj->images()->orderBy('priority', 'desc')->first();
        if ($thumb) {
            return [
                'id' => $thumb->id,
                'url' => _MediaHelper::getURL($thumb->url, 'image'),
            ];
        }
        return [];

    }
}
