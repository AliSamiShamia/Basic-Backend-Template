<?php

namespace App\Helper;

use App\Models\ParamType;
use App\Models\ProductParma;
use App\Services\Facades\FProduct;
use Illuminate\Support\Facades\Log;

class _Core
{
    public static function generateRandomString($id, $length = 3)
    {
        return uniqid("JW-") . str_pad($id, 8, '0', STR_PAD_LEFT);
    }

    public static function getFilterParma()
    {
        $types = ParamType::query()->whereHas('ProductParma')->get();

        $list = [];
        foreach ($types as $type) {
            $values = $type->ProductParma()->select('value', 'type_id')->groupBy('value', 'type_id')->get()->toArray();
            $list[] = [
                'id' => $type->id,
                'title' => $type->param,
                'values' => $values
            ];
        }
        return $list;

    }

    public static function getParams($product): array
    {
        $types = ParamType::query()->whereHas('ProductParma', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })->get();
        $list = [];
        foreach ($types as $type) {
            $values = $type->ProductParma()->where([
                'product_id' => $product->id
            ])->select('value', 'id')->distinct('value')->get()->toArray();
            $list[] = [
                'id' => $type->id,
                'title' => $type->param,
                'values' => $values
            ];
        }
        return $list;
    }

    public static function getParamType($product): array
    {
        $types = ParamType::query()->whereHas('ProductParma', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })->get();
        $list = [];
        foreach ($types as $type) {
            $values = $type->ProductParma()->where([
                'product_id' => $product->id
            ])->select('value', 'id')->get()->toArray();
            $list[] = [
                'id' => $type->id,
                'title' => $type->param,
                'values' => $values
            ];
        }
        return $list;
    }

    public static function getMinMaxPrice($price)
    {
        $min = $price['min'] ?? 0;
        $max = $price['max'] ?? 0;
        return [$min, $max];
    }

    public static function getFilters($filters)
    {
        $productIDs = [];
        foreach ($filters as $key => $filter) {

            $value = $filter['value'];
            $type_id = $filter['type_id'];
            $type = ParamType::query()->where([
                'id' => $type_id
            ])->first();
            if (!$type) {
                return [];
            }
            $listOfProduct = ProductParma::query()->where([
                'value' => $value,
                'type_id' => $type->id
            ])->get()->pluck('product_id')->toArray();
            $productIDs[] = $listOfProduct;
        }
//        return $productIDs;
        return call_user_func_array('array_merge', $productIDs);
    }

    public static function getPriceRangeParam()
    {
        [$minPrice, $maxPrice] = self::getPriceRange();
        return ["min" => $minPrice, "max" => $maxPrice];
    }

    public static function getPriceRange()
    {
        $obj = (new FProduct())->getByColumns([
            'is_live' => true,
        ]);
        $minPrice = $obj->min('price');
        $maxPrice = $obj->max('price');
        return [$minPrice, $maxPrice];
    }
}
