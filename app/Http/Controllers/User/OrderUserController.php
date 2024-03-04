<?php

namespace App\Http\Controllers\User;

use App\Helper\_DPOHelper;
use App\Helper\_EmailHelper;
use App\Helper\_OrderHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Resources\User\OrderResource;
use App\Services\Interfaces\IOrder;
use App\Services\Interfaces\IUser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderUserController extends Controller
{
    private $order, $user;

    /**
     * @param IOrder $order
     * @param IUser $user
     */
    public function __construct(IOrder $order, IUser $user)
    {
        $this->order = $order;
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return BaseResource|JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $res = $this->order->index($request);
            return OrderResource::paginable($res);
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    public function invoice($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $order = $this->order->getByColumns([
                'id' => $id,
                'user_id' => $user->id
            ])->first();

            if ($order) {
                $res = _OrderHelper::invoice($order);
                return BaseResource::create([
                    'url' => $res
                ]);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            Log::error($exception);
            return BaseResource::exception($exception);
        }
    }

    public function orderSubmitted(Request $request, $id, $token)
    {
        try {

            $user = _EmailHelper::getUserPerToken($token);
            if ($user) {
                $order = $this->order->getById($id);
                if ($order) {
                    $res = _DPOHelper::verifyToken($request);
                    if ($res) {
                        $order->update([
                            'status' => 'completed'
                        ]);
                        $file_name = md5($order->tracking_number . "-order-" . $order->id) . '.pdf';
                        $res = _OrderHelper::invoice($order);
                        _EmailHelper::sendToMail($user->email, [
                            "name" => $user->first_name . " " . $user->last_name,
                            "tracking_number" => $order->tracking_number
                        ], "emails.invoice", "Invoice", [
                            [
                                "url" => $res
                            ]
                        ]);
                    }
                }
            }

            return redirect()->to(getenv('_Base_URL') . '/account');
        } catch (Exception $exception) {
            Log::error($exception);
            return redirect()->to(getenv('_Base_URL') . '/account');
        }
    }

    public function orderStore()
    {
        $user = $this->user->getById(1);
        $res = _DPOHelper::CreateToken($user, 1, 20);
        return BaseResource::create([
            'url' => $res
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return BaseResource|JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            DB::beginTransaction();
            $order = $this->order->placeOrder($request);
            if ($order) {
                $res = _DPOHelper::CreateToken($user, $order->id, $order->total_amount);
                if ($res) {
                    DB::commit();
                    return BaseResource::create([
                        'url' => $res
                    ]);
                }
            }
            DB::rollBack();
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
