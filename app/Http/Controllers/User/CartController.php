<?php

namespace App\Http\Controllers\User;

use App\Helper\_RuleHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Resources\User\CartResource;
use App\Services\Interfaces\ICart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class CartController extends Controller
{

    private $cart;

    public function __construct(ICart $cart)
    {
        $this->cart = $cart;
    }


    /**
     * Display a listing of the resource.
     *
     * @return BaseResource|JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $res = $this->cart->index($request);
            return CartResource::collection($res);
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    public function changeQuantity(Request $request, $id)
    {
        try {
            $rules = _RuleHelper::getRule('update_quantity');
            $request->validate($rules);
            $this->cart->updateItem($request, $id);
            $res = $this->cart->index($request);
            if ($res) {
                return CartResource::collection($res);
            }
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return CartResource|JsonResponse|Response
     */
    public function store(Request $request)
    {
        try {
            $rules = _RuleHelper::getRule('add_to_cart');
            $request->validate($rules);
            $res = $this->cart->addItem($request);
            if ($res) {
                DB::commit();
                return CartResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return CartResource|JsonResponse|Response
     */
    public function show($id)
    {
        try {
            $res = $this->cart->getById($id);
            return CartResource::create($res);
        } catch (\Exception $exception) {
            return BaseResource::exception($exception);

        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return CartResource|JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $cart = $this->cart->update($request, $id);
            return CartResource::create($cart);
        } catch (\Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return BaseResource|JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        try {
            $this->cart->removeItem($id);
            $res = $this->cart->index($request);
            if ($res) {
                return CartResource::collection($res);
            }
            return BaseResource::return();
        } catch (\Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    public function removeAll()
    {
        try {
            $res = $this->cart->removeAll();
            if ($res) {
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (\Exception $exception) {
            return BaseResource::exception($exception);

        }
    }
}
