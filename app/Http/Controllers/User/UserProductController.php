<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Resources\User\ProductResource;
use App\Services\Interfaces\IProduct;
use Exception;
use Illuminate\Http\Request;

class UserProductController extends Controller
{
    private $product;

    public function __construct(IProduct $product)
    {
        $this->product = $product;
    }

    public function index(Request $request)
    {
        try {
            $res = $this->product->index($request);
            return ProductResource::paginable($res);
        } catch (Exception $exp) {
            return BaseResource::exception($exp);
        }
    }
}
