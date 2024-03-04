<?php

namespace App\Http\Controllers\User;

use App\Helper\_Core;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Resources\User\ProductResource;
use App\Services\Interfaces\IProduct;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    private $product;

    /**
     * @param IProduct $product
     */
    public function __construct(IProduct $product)
    {
        $this->product = $product;
    }


    /**
     * Display a listing of the resource.
     *
     * //     * @return BaseResource|JsonResponse|AnonymousResourceCollection|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(Request $request)
    {
        try {
            $res = $this->product->index($request);
            $products = ProductResource::paginableWithData($res);
            return BaseResource::create([
                'products' => ProductResource::dataCollection($products),
                'filter' => _Core::getFilterParma(),
                'price_range' => _Core::getPriceRangeParam(),
            ]);
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * //     * @return BaseResource|JsonResponse|AnonymousResourceCollection|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function blankList(Request $request)
    {
        try {
            $res = $this->product->index($request);
            return ProductResource::paginable($res);
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return ProductResource|JsonResponse
     */
    public function show($id)
    {
        try {
            $res = $this->product->getById($id);
            if ($res) {
                return ProductResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param string $slug
     * @return ProductResource|JsonResponse
     */
    public function getBySlug($slug)
    {
        try {
            $res = $this->product->getBySlug($slug);
            if ($res) {
                return ProductResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

}
