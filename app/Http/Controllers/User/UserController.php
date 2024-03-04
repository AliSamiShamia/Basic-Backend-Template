<?php

namespace App\Http\Controllers\User;

use App\Helper\_OTPHelper;
use App\Helper\_RuleHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Resources\User\UserResource;
use App\Services\Interfaces\IUser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    private $user;

    /**
     * @param $user
     */
    public function __construct(IUser $user)
    {
        $this->user = $user;
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


    public function login(Request $request)
    {
        try {
            $user_name = $request->input('user_name');
            $user = null;
            if (filter_var($user_name, FILTER_VALIDATE_EMAIL)) {
                $request->validate(_RuleHelper::getRule('loginByEmail'));
                $user = $this->user->loginByEmail($request);
            } else if (preg_match("/^[0-9]+$/", $user_name) && $request->has('country_code')) {
                $request->validate(_RuleHelper::getRule('loginByPhone'));
                $user = $this->user->login($request);
            }
            if ($user) {
                return UserResource::create($user);
            } else {
                $user = $this->user->register($request);
                if ($user) {
                    return UserResource::create($user);
                }
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::errors($exception->getMessage());
        }
    }

    public function loginByEmail(Request $request)
    {
        try {
            $request->validate(_RuleHelper::getRule('loginByEmail'));
            $user = $this->user->loginByEmail($request);
            if ($user) {
                return UserResource::create($user);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::errors($exception->getMessage());
        }
    }

    public function register(Request $request)
    {
        try {
            $user_name = $request->input('user_name');
            $is_email = filter_var($user_name, FILTER_VALIDATE_EMAIL);
            $is_phone = preg_match("/^[0-9]+$/", $user_name);
            if ($is_email) {
                $request->validate(_RuleHelper::getRule('registerByEmail'));
            } else if ($is_phone) {
                $request->validate(_RuleHelper::getRule('registerByPhone'));
            } else {
                return BaseResource::return();
            }
            DB::beginTransaction();
            $user = $this->user->register($request);
            if ($user) {
                [$otp, $user_name] = $this->user->sendOtp($request, $user);
                DB::commit();
                return BaseResource::create([
                    'user_id' => $user->id,
                    'user_name' => $user_name
                ]);

            }
            return BaseResource::return();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return BaseResource::return($exception->getMessage());
        }
    }

    public function sendOTP(Request $request)
    {
        try {
            $user_name = $request->input('user_name');
            $is_email = filter_var($user_name, FILTER_VALIDATE_EMAIL);
            $is_phone = preg_match("/^[0-9]+$/", $user_name);
            if ($is_email) {
                $request->validate(_RuleHelper::getRule('otp_for_email'));
            } else if ($is_phone) {
                $request->validate(_RuleHelper::getRule('otp_for_phone'));
            } else {
                return BaseResource::return();
            }
            $user = $this->user->getById($request->input('user_id'));
            if ($user) {
                [$otp, $user_name] = $this->user->sendOtp($request, $user);
                if ($otp) {
                    $user = $otp->User()->first();
                    return BaseResource::create([
                        'user_id' => $user->id,
                        'user_name' => $user_name
                    ]);
                }
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::errors($exception->getMessage());
        }
    }

    public function checkOTP(Request $request)
    {
        try {
            $request->validate(_RuleHelper::getRule('check_otp'));
            $user = $this->user->getById($request->input('user_id'));
            if ($user) {
                $check = $this->user->checkOtp($request, $user);
                if ($check) {
                    return UserResource::create($check->User()->first());
                }
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::errors($exception->getMessage());
        }
    }


    public function updateInfo(Request $request): JsonResponse|BaseResource
    {
        try {
            $request->validate(_RuleHelper::getRule('user-info'));
            $res = $this->user->updateInfo($request);
            if ($res) {
                return UserResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return();
        }
    }

    public function updateSecurityInfo(Request $request): JsonResponse|BaseResource
    {
        try {
            $request->validate(_RuleHelper::getRule('security-info'));
            $res = $this->user->updateSecurityInfo($request);
            if ($res) {
                return UserResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            Log::error($exception);
            return BaseResource::return();
        }
    }

    public function contact(Request $request)
    {
        try {

            $rules = [
                'name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'msg' => 'required',
            ];
            $request->validate($rules);
            $res = $this->user->contact($request);
            if ($res) {
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return();
        }


    }
}
