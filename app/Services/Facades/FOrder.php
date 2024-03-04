<?php

namespace App\Services\Facades;

use App\Helper\_Core;
use App\Helper\_RuleHelper;
use App\Models\Cart;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Interfaces\IOrder;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FOrder extends FBase implements IOrder
{
    public function __construct()
    {
        $this->model = Order::class;

        $this->rules = [
            'user_address_id' => _RuleHelper::_Rule_Require,
            'tracking_number' => _RuleHelper::_Rule_Require,
            'total_amount' => _RuleHelper::_Rule_Require,
        ];
        $this->columns = ['user_address_id', 'tracking_number', 'total_amount', 'status', 'user_id'];
        $this->orderBy = "desc";
    }

    public function index(Request $request)
    {
        $this->props = [
            ["status", '<>', 'initialized']
        ];
        if ($request->has('order_status')) {
            $this->props = [
                "status" => $request->order_status
            ];
        }
        $user = Auth::guard('api')->user();
        $orders = $this->getByColumns([
            ["status", '<>', 'initialized']
        ]);
        if ($request->has('start_date')) {
            $orders = $orders->whereDate(
                'created_at', '>=', $request->input('start_date')
            );
        }
        if ($request->has('end_date')) {
            $orders = $orders->whereDate(
                'created_at', '<=', $request->input('end_date')
            );
        }
        if ($user->role == "admin") {
            return $orders->orderBy('created_at','desc')->get();
        }
        return $orders->where([
            'user_id' => $user->id
        ])->orderBy('created_at','desc')->get();

    }

    public function trackOrder($order_id)
    {
        $order = $this->getById($order_id);
        if ($order) {
            return $order->status;
        }
        return null;
    }

    public function updateStatus($order_id, $status)
    {

        $order = $this->getById($order_id);
        if ($order) {
            return $order->update([
                'status' => $status
            ]);
        }
        return null;
    }

    public function orderItems(Request $request, $order_id)
    {
        $order = $this->getById($order_id);
        if ($order) {
            $items = $request->input('items');
            if (!$items) {
                return false;
            }
            foreach ($items as $item) {
                $product = Product::query()->where([
                    'id' => $item['product_id']
                ]);
                if ($product) {
                    $order->OrderItem()->create([
                        'product_id' => $product->id,
                        'discount_id' => $product->discount_id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                    ]);
                }
            }
        }
    }

    public function placeOrder(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            $address = $user->addresses()->where('is_default', true)->first();
            if (!$address) {
                throw new Exception('Address is  not available!');
            }
            $order = Order::create([
                "status" => "initialized",
                "user_id" => $user->id,
                "user_address_id" => $address->id,
                "total_amount" => 0,
            ]);
            $total_amount = 0;
            $cart = Cart::query()->where([
                'user_id' => $user->id,
            ])->get();

            if (!$cart || count($cart) == 0) {
                throw new Exception('The cart is empty!');
            }

            foreach ($cart as $item) {
                $product = Product::query()->find($item->product_id);
                if ($product->stock < $item->quantity) {
                    throw new Exception('Some items in your order are no longer available, please check you cart again');
                }
                $product->update([
                    'stock' => $product->stock - $item->quantity,
                ]);
                $price = $product->price;
                $discount_value = 0;
                if ($item->discount_id) {
                    $discount = Discount::query()->where('id', $item->discount_id);
                    if ($discount) {
                        $discount_value = $discount->discount_percent;
                    }
                }
                $total_amount += ($item->quantity * $price) * ((100 - $discount_value) / 100);
                $orderItem = $order->orderItems()->create([
                    'product_id' => $item->product_id,
                    'discount_id' => $item->discount_id,
                    'quantity' => $item->quantity,
                    'price' => $price * ((100 - $discount_value) / 100),
                ]);
                if ($orderItem) {
                    foreach ($item->CartItem()->get() as $param) {
                        $orderItem->OrderItemParam()->create([
                            'order_item_id' => $orderItem->id,
                            'param_id' => $param->param_id,
                            'value' => $param->ProductParam->value,
                            'order_id' => $order->id
                        ]);
                    }
                }
            }
            $order->update([
                'total_amount' => $total_amount
            ]);

            // empty the cart after placing the order
            Cart::query()->where([
                'user_id' => $user->id,
            ])->delete();
            return $order;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $record = Order::query()->findOrFail($id);
            if ($record->status == 'processing') {
                $res = $record->update([
                    'status' => 'cancelled',
                ]);
                if ($res) {
                    $order_items = $record->orderItems;
                    foreach ($order_items as $order_item) {
                        $product = $order_item->product;
                        $quantity = $order_item->quantity;
                        $product->update([
                            "stock" => $product->stock + $quantity,
                        ]);
                    }
                    DB::commit();
                    return true;
                }
            }
            DB::rollBack();
            return false;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
