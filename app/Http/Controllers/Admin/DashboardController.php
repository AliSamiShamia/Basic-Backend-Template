<?php

namespace App\Http\Controllers\Admin;

use App\Helper\_RoleHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\OrderResource;
use App\Http\Resources\BaseResource;
use App\Services\Interfaces\IOrder;
use App\Services\Interfaces\IUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{

    private IUser $user;
    private IOrder $order;

    public function __construct(IOrder $order, IUser $user)
    {
        $this->user = $user;
        $this->order = $order;
    }

    public function index(Request $request)
    {
        $orders = $this->order->getByColumns([
            ["status", '<>', 'initialized']
        ]);
        $user = $this->user->getByColumns([
            'role' => _RoleHelper::_USER_ROLE,
        ]);
        if ($request->has('start_date')) {
            $orders = $orders->whereDate(
                'created_at', '>=', $request->input('start_date')
            );
            $user = $user->whereDate(
                'created_at', '>=', $request->input('start_date')
            );
        }
        if ($request->has('end_date')) {
            $orders = $orders->whereDate(
                'created_at', '<=', $request->input('end_date')
            );
            $user = $user->whereDate(
                'created_at', '<=', $request->input('end_date')
            );
        }
        $result = $orders->select(DB::raw('SUM(total_amount) as amount, COUNT(*) as order_count'))->first();
        $user = $user->count();

        return BaseResource::create([

                [
                    "stats" => $result->order_count,
                    "color" => 'info',
                    "title" => 'Orders',
                    "icon" => 'mdi:trending-up'
                ],
                [
                    "stats" => "$" . (max($result->amount, 0)),
                    "color" => 'warning',
                    "title" => 'Total Sales',
                    "icon" => 'mdi:cellphone-link'
                ],
                [
                    "stats" => $user > 1000 ? number_format($user / 1000, 1) . 'k' : $user,
                    "color" => 'success',
                    "title" => 'Customers',
                    "icon" => 'mdi:account-outline'
                ],


        ]);
    }


}
