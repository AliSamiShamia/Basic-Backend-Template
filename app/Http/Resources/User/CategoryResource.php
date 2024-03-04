<?php

namespace App\Http\Resources\User;

use App\Helper\_Core;
use App\Helper\_MediaHelper;
use App\Http\Resources\BaseResource;
use App\Models\ParamType;
use App\Models\ProductParma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class CategoryResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $parent = $this->Parent()->first();
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'parent' => $parent ? $parent->name : null,
            'products' => $this->listOfProducts($this, $request),
            'filter' => $this->filters(),
            'price_range' => $this->priceRange(),

            'url' => $this->media($this)
        ];
    }

    public function listOfProducts($category, Request $request)
    {

        $name = Route::currentRouteName(); // string
        $products = [];
        if (in_array($name, ["category.show", "category.show_by_slug"])) {
            $filters = $request->input('filter');
            $productIDs = [];
            if ($filters) {
                $productIDs = _Core::getFilters($filters);
            }
            $products = $category->Products();
            if ($request->has('price')) {
                $price = $request->input('price');
                [$min, $max] = _Core::getMinMaxPrice($price);
                $products = $products->whereBetween('products.price', [$min, $max]);
            }
            if (count($productIDs)) {
                $uniqueProductList = array_unique($productIDs);
                $products = $products->whereIn('products.id', $uniqueProductList);
            }
            $products = $products->get();
//            Log::error($products);
        }
        return ProductResource::dataCollection($products)->jsonSerialize();
    }

    public function filters()
    {
        $name = Route::currentRouteName(); // string
        if (in_array($name, ["category.show", "category.show_by_slug"])) {
            return _Core::getFilterParma();
        }
        return [];
    }

    public function priceRange()
    {
        $name = Route::currentRouteName(); // string
        if (in_array($name, ["category.show", "category.show_by_slug"])) {
            return _Core::getPriceRangeParam();
        }
        return [];
    }


    public function media($obj)
    {
        $banner = $obj->allMedia()->first();
        if ($banner) {
            return _MediaHelper::getURL($banner->url, 'image');
        }
        return null;
    }
}
