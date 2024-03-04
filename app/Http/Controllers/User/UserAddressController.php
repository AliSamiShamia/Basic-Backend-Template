<?php

namespace App\Http\Controllers\User;

use App\Helper\_RuleHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Resources\User\UserAddressResource;
use App\Services\Interfaces\IUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserAddressController extends Controller
{
    private $user;

    /**
     * @param IProduct $product
     */
    public function __construct(IUser $user)
    {
        $this->user = $user;
    }


    /**
     * Display a listing of the resource.
     *
     * //     * @return array|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();

            if ($user) {
                $res = $user->addresses()->get();

                return UserAddressResource::collection($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    public function getDefaultAddress(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();

            if ($user) {
                $res = $user->addresses()->where('is_default', 1)->first();
                return UserAddressResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    public function changeDefaultAddress(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();

            if ($user) {
                $res = $user->addresses()->where('id', $id)->first();
                if ($res) {
                    $user->addresses()->update([
                        'is_default' => false
                    ]);
                    $res->update([
                        'is_default' => true
                    ]);
                }
                return UserAddressResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate(_RuleHelper::getRule('address'));
            $res = $this->user->address($request);
            if ($res) {
                return UserAddressResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    public function setDefaultAddress(Request $request, $id)
    {
        try {
            $res = $this->user->setDefaultAddress($request, $id);
            if ($res) {
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $request->validate(_RuleHelper::getRule('address'));
            $res = $this->user->updateAddress($request, $id);
            if ($res) {
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }
}
