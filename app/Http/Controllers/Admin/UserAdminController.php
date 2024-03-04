<?php

namespace App\Http\Controllers\Admin;

use App\Helper\_RoleHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\OrderResource;
use App\Http\Resources\Admin\UserAddressResource;
use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\BaseResource;
use App\Services\Interfaces\IUser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserAdminController extends Controller
{

    private $user;

    /**
     * @param $user
     */
    public function __construct(IUser $user)
    {
        $this->user = $user;
    }

    public function index(Request $request)
    {
        try {
            $res = $this->user->getByColumns([
                'role' => 'user',
            ])->get();
            return UserResource::paginable($res);
        } catch (Exception $exception) {
            return BaseResource::return();
        }
    }

    public function addresses($id)
    {
        try {
            $user = $this->user->getById($id);
            if ($user) {
                $addresses = $user->addresses()->get();
                return UserAddressResource::paginable($addresses);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return();
        }
    }

    public function orders($id)
    {
        try {
            $user = $this->user->getById($id);
            if ($user) {
                $orders = $user->orders()->get();
                return OrderResource::paginable($orders);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return();
        }
    }

    public function changePermission($id)
    {
        try {
            $res = $this->user->getById($id);
            if ($res) {
                $res->update([
                    'has_permission' => !$res->has_permission
                ]);
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return();
        }
    }

    public function show($id)
    {
        try {
            $res = $this->user->getById($id);
            if ($res) {
                return UserResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return();
        }
    }

    public function login(Request $request)
    {
        try {
            $user = $this->user->getByColumns([
                'email' => $request->input('email'),
                'role' => _RoleHelper::_ADMIN_ROLE,
            ])->first();
            if ($user) {
                if (Hash::check($request->input('password'), $user->password))
                    return UserResource::create($user);
            }
            Log::error($request);
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::errors($exception->getMessage());
        }
    }

    /**
     * User Info.
     *
     * @param Request $request
     * @return UserResource|JsonResponse
     */
    public function profile(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if ($user) {
                return UserResource::create($user);
            } else {
                return BaseResource::return(401);
            }
        } catch (Exception $exception) {
            return BaseResource::return();
        }
    }

}
