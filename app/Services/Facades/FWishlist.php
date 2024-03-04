<?php

namespace App\Services\Facades;

use App\Helper\_RuleHelper;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Wishlist;
use App\Services\Interfaces\IWishlist;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FWishlist extends FBase implements IWishlist
{
    public function __construct()
    {
        $this->model = Wishlist::class;
        $this->rules = [
            'product_id' => _RuleHelper::_Rule_Require,
        ];
        $this->columns = ['product_id'];
    }

    public function index(Request $request)
    {
        return Wishlist::query()->where([
            'user_id' => Auth::guard('api')->id()
        ])->get();
    }

    public function addItem(Request $request)
    {
        $product_id = $request->input('product_id');
        $product = (new FProduct())->getById($product_id);
        if (!$product) {
            throw new Exception("Product Not Available Now!");
        }
        $wishlist = Wishlist::query()->where([
            'product_id' => $product->id,
            'user_id' => Auth::guard('api')->id()
        ])->first();
        if (!$wishlist) {
            return Wishlist::query()->create([
                'user_id' => Auth::guard('api')->id(),
                'product_id' => $product->id,
            ]);
        }
        return $wishlist;
    }

    public function removeItem($id)
    {
        $wishlist = Wishlist::query()->where([
            'product_id' => $id,
            'user_id' => Auth::guard('api')->id(),
        ]);
        return $wishlist->delete();
    }

    public function moveToCart($id)
    {
        $item = Wishlist::query()->findOrFail($id);
        $product_id = $item->product_id;
        $product = Product::query()->where([
            'id' => $product_id,
            ['stock', '>=', 0]
        ])->first();
        if (!$product) {
            throw new Exception("Product Not Available Now!");
        }
        $cart = Cart::query()->where([
            'product_id' => $product->id,
            'user_id' => Auth::guard('api')->id()
        ])->first();
        if ($cart) {
            $cart->update([
                'quantity' => $cart->quantity + 1
            ]);
        } else {
            $cart = Cart::query()->create([
                'user_id' => Auth::guard('api')->id(),
                'product_id' => $product->id,
                'quantity' => 1,
            ]);
        }
        $this->removeItem($id);
        return $cart;
    }

    public function removeAll()
    {
        return Wishlist::query()->where([
            'user_id' => Auth::guard('api')->id(),
        ])->delete();
    }

    public function total()
    {
        return Wishlist::query()->where([
            'user_id' => Auth::guard('api')->id(),
        ])->get()->sum(function ($cart) {
            return $cart->cartItems->sum(function ($cartItem) {
                return $cartItem->quantity * $cartItem->product->price;
            });
        });
    }
}
