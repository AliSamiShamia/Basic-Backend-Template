<?php

namespace App\Http\Controllers\Admin;

use App\Helper\_OrderHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\OrderResource;
use App\Http\Resources\BaseResource;
use App\Services\Interfaces\IOrder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class OrderAdminController extends Controller
{
    private $order;

    /**
     * @param IOrder $order
     */
    public function __construct(IOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Display a listing of the resource.
     *
     * @return BaseResource|JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $orders = $this->order->getByColumns([
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

            $res = $orders->get();
            return OrderResource::paginable($res);
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    public function invoice($id)
    {
        try {
            $order = $this->order->getById($id);
            if ($order) {
                $res = _OrderHelper::invoice($order);
                return BaseResource::create([
                    'url' => $res
                ]);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    public function show($id)
    {
        try {
            $order = $this->order->getById($id);
            if ($order) {
                return OrderResource::create($order);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);

        }
    }

}
