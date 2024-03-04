<?php

namespace App\Services\Facades;

use App\Helper\_Core;
use App\Helper\_RuleHelper;
use App\Models\Product;
use App\Services\Interfaces\IProduct;
use Illuminate\Http\Request;

class FProduct extends FBase implements IProduct
{

    public function __construct()
    {
        $this->model = Product::class;
        $this->rules = [
            'name' => _RuleHelper::_Rule_Require,
            'price' => _RuleHelper::_Rule_Require,
        ];
        $this->columns = ['name', 'description', 'brief', 'price', 'pre_price', 'weight', 'stock', 'is_trending', 'is_live', 'is_featured', 'discount_id'];
    }

    public function index(Request $request)
    {
        $res = Product::query();
        if ($request->input('categories')) {
            $categories = $request->input('categories');
            $res->whereHas('Categories', function ($q) use ($categories) {
                $q->whereIn('categories.slug', $categories);
            });
        }
        if ($request->input('featured')) {
            $res->where('is_featured', true);
        }
        if ($request->input('trending')) {
            $res->where('is_trending', true);
        }
        if ($request->input('live')) {
            $res->where('is_live', $request->input('live'));
        } else {
            $res->where('is_live', true);
        }
        $res->orderBy('is_featured', 'DESC');
        $res->orderBy('is_trending', 'DESC');
        $res->orderBy('updated_at', 'DESC');
        $filters = $request->input('filter');

        if ($filters) {
            $productIDs = _Core::getFilters($filters);
            $uniqueProductList = array_unique($productIDs);
            $res = $res->whereIn('id', $uniqueProductList);
        }
        if ($request->has('price')) {
            $price = $request->input('price');
            [$min, $max] = _Core::getMinMaxPrice($price);
            $res = $res->whereBetween('price', [$min, $max]);
        }
        return $res->get();
    }

}
