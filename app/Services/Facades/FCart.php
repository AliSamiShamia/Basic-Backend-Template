<?php

namespace App\Services\Facades;

use App\Helper\_RuleHelper;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ParamType;
use App\Models\Product;
use App\Models\ProductParma;
use App\Services\Interfaces\ICart;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FCart extends FBase implements ICart
{
    public function __construct()
    {
        $this->model = Cart::class;
        $this->rules = [
            'product_id' => _RuleHelper::_Rule_Require,
            'quantity' => _RuleHelper::_Rule_Require,
        ];
        $this->columns = ['product_id', 'quantity'];
    }

    public function index(Request $request)
    {
        return Cart::query()->where([
            'user_id' => Auth::guard('api')->id()
        ])->get();
    }

    public function addItem(Request $request)
    {

        $quantity = $request->input('quantity');
        $product_id = $request->input('product_id');
        $user = Auth::guard('api')->id();
        $product = Product::query()->where([
            'id' => $product_id,
            ['stock', '>=', $quantity]
        ])->first();
        if (!$product) {
            throw new Exception("Product Not Available Now!");
        }
        $options = [];
        if ($product->ProductParams()->count() > 0) {
            $rules = _RuleHelper::getRule('add_to_cart_product_options');
            $request->validate($rules);
            $options = $request->input('options');
            if (count($options) == 0) {
                throw new Exception("Product params are not available");
            }
        }
        $cart = Cart::query();
        $conditions = [];
        if (count($options) > 0) {
            foreach ($options as $option) {
                $param = ProductParma::query()->where([
                    'product_id' => $product->id,
                    'id' => $option['value_id']
                ])->first();
                if (!$param) {
                    return null;
                }
                $conditions[] = $param->id;
                $cart = $cart->whereHas('CartItem', function ($query) use ($param) {
                    $query->where('param_id', $param->id);
                });
            }
        }
        $cart = $cart->where([
            'product_id' => $product->id,
            'user_id' => Auth::guard('api')->id()
        ])->first();
        if ($cart) {
            DB::beginTransaction();
            $cart->update([
                'quantity' => $cart->quantity + $quantity
            ]);
            DB::commit();
            return $cart;
        } else {
            DB::beginTransaction();
            $cart = Cart::query()->create([
                'user_id' => Auth::guard('api')->id(),
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
            if ($cart) {
                foreach ($conditions as $item) {
                    $cart->CartItem()->create([
                        'param_id' => $item,
                    ]);
                }
                DB::commit();
                return $cart;
            } else {
                DB::rollBack();
            }
        }
//        DB::rollBack();

        return null;
    }

    public function updateItem(Request $request, $id)
    {
        $quantity = $request->input('quantity');
        $cart = Cart::query()->where([
            'id' => $id,
            'user_id' => Auth::guard('api')->id()
        ])->first();
        if ($cart) {
            $product = $cart->Product;
            if (!$product) {
                throw new Exception("Product Not Available Now!");
            }
            if (($product->stock + $cart->quantity) >= $quantity)
                $cart->update([
                    'quantity' => $quantity
                ]);

            return $cart;
        } else {
            return null;
        }
    }


    public function update(Request $request, $id)
    {
        $cart = $this->getById($id);
        if (!$cart) {
            throw new Exception("Item Not Available!");
        }
        $quantity = $request->input('quantity');
        $product = Product::query()->where([
            'id' => $cart->product_id,
            ['stock', '>=', $quantity]
        ])->first();
        if (!$product) {
            throw new Exception("Product Not Available Now!");
        }
        $cart->update([
            'quantity' => $quantity
        ]);
        return $cart;
    }

    public function removeItem($id)
    {
        return Cart::query()->where([
            'id' => $id,
            'user_id' => Auth::guard('api')->id(),
        ])->delete();
    }

    public function removeAll()
    {
        return Cart::query()->where([
            'user_id' => Auth::guard('api')->id(),
        ])->delete();
    }

    public function total()
    {
        return Cart::query()->where([
            'user_id' => Auth::guard('api')->id(),
        ])->get()->sum(function ($cart) {
            return $cart->cartItems->sum(function ($cartItem) {
                return $cartItem->quantity * $cartItem->product->price;
            });
        });
    }
}
