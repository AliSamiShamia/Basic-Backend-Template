<?php

namespace App\Services\Facades;

use App\Helper\_EmailHelper;
use App\Helper\_OTPHelper;
use App\Helper\_RoleHelper;

use App\Models\User;
use App\Models\UserOtp;
use App\Services\Interfaces\IUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class FUser extends FBase implements IUser
{

    public function __construct()
    {
        $this->model = User::class;

        $this->columns = [
            'first_name',
            'middle_name',
            'last_name',
            'country',
            'country_code',
            'phone_number'
        ];
    }

    public function login(Request $request)
    {
        try {
            return $this->getByPhone($request);
        } catch (Exception $exception) {
            throw new Exception($exception);
        }
    }

    public function loginByEmail(Request $request)
    {
        $user = User::query()->where([
            'email' => $request->email,
            'role' => _RoleHelper::_USER_ROLE,
        ])->first();
        if ($user) {
            $otp = _OTPHelper::generateOtp();
            UserOtp::query()->create([
                'email' => $user->email,
                'otp' => Hash::make($otp),
            ]);
            _OTPHelper::sendToMail($user->email, $otp);
            return $user;
        }
    }

    public function adminLogin(Request $request)
    {
        try {
            return $this->getByEmail($request);
        } catch (Exception $exception) {
            throw new Exception($exception);
        }
    }

    public function register(Request $request)
    {

        $user_name = $request->input('user_name');
        $is_email = filter_var($user_name, FILTER_VALIDATE_EMAIL);
        $is_phone = preg_match("/^[0-9]+$/", $user_name);
        $conditions = [
            'role' => _RoleHelper::_USER_ROLE
        ];

        if ($is_email) {
            $conditions['email'] = $user_name;
        } else if ($is_phone && $request->has('country_code')) {
            $conditions['phone_number'] = _OTPHelper::trimPhoneNumber($user_name);
            $conditions['country_code'] = $request->input('country_code');
        } else {
            throw new Exception("Data is not correct", 400);
        }
        $check = $this->getByColumns($conditions)->first();
        if ($check) {
            return $check;
        } else {
            if ($is_phone) {
                return User::query()->create([
                    'country_code' => $request->input('country_code'),
                    'phone_number' => _OTPHelper::trimPhoneNumber($user_name),
                ]);
            } elseif ($is_email) {
                return User::query()->create([
                    'email' => $user_name,
                ]);
            }
        }
        return null;

    }

    public function getByPhone(Request $request)
    {
        $phone = _OTPHelper::trimPhoneNumber($request->input('phone_number'));
        $code = $request->input('country_code');
        return User::query()->where([
            'phone_number' => $phone,
            'country_code' => $code,
            'role' => _RoleHelper::_USER_ROLE,
        ])->first();
    }


    public function getByEmail(Request $request)
    {
        $user = User::query()->where([
            'email' => $request->email,
            'role' => _RoleHelper::_ADMIN_ROLE,
        ])->first();
        if ($user && Hash::check($request->password, $user->password)) {
            return $user;
        }
    }

    public function sendOtp(Request $request, $user)
    {
        $user_name = $request->input('user_name');
        $is_email = filter_var($user_name, FILTER_VALIDATE_EMAIL);
        $is_phone = preg_match("/^[0-9]+$/", $user_name);
        $otp = _OTPHelper::generateOtp();
        if ($is_email) {
            UserOtp::query()->where([
                'email' => $user_name,
                'user_id' => $user->id
            ])->update([
                'status' => false,
            ]);
            $userOtp = UserOtp::query()->create([
                'email' => $user_name,
                'user_id' => $user->id,
                'expired_at' => Carbon::now()->addHours(3),
                'otp' => Hash::make($otp),
            ]);
            _OTPHelper::sendToMail($user_name, $otp);
            if ($userOtp) {
                return [$userOtp, $user_name];
            }
        } else if ($is_phone) {
            $temp_code = $request->input('country_code');
            $phone_number = _OTPHelper::preparePhoneNumber($user_name, $temp_code);
            UserOtp::query()->where([
                'phone_number' => $phone_number,
                'user_id' => $user->id
            ])->update([
                'status' => false,
            ]);
            $userOtp = UserOtp::query()->create([
                'phone_number' => $phone_number,
                'user_id' => $user->id,
                'expired_at' => Carbon::now()->addHours(3),
                'otp' => Hash::make($otp),
            ]);
            _OTPHelper::send($phone_number, $otp);
            if ($userOtp) {
                return [$userOtp, $phone_number];
            }

        }
        return [null, null];
    }

    public function checkOtp(Request $request, $user)
    {

        $user_name = $request->input('user_name');
        $otp = $request->input('otp');
        $is_email = filter_var($user_name, FILTER_VALIDATE_EMAIL);
        $is_phone = preg_match("/^(?:\+\d{1,4})?\d{8,14}$/", $user_name);
        $userOtp = UserOtp::query();
        if ($is_phone) {
            $userOtp = $userOtp->where([
                'phone_number' => $user_name,
                'user_id' => $user->id,
                'status' => true,
                ['expired_at', '>', Carbon::now()]
            ]);
        } else if ($is_email) {
            $userOtp = $userOtp->where([
                'email' => $user_name,
                'user_id' => $user->id,
                'status' => true,
                ['expired_at', '>', Carbon::now()]
            ]);
        } else {
            return null;
        }
        $userOtp = $userOtp->first();
        if ($userOtp) {
            $check = Hash::check($otp, $userOtp->otp);
            if ($check) {
                $userOtp->update([
                    'status' => false,
                ]);
            }
            return $userOtp;
        }
        return null;

    }

    public function getById($id)
    {
        return User::query()->find($id);
    }

    public function address(Request $request)
    {
        $user = Auth::guard('api')->user();
        $address = $this->prepareAddressObj($request);
        return $user->addresses()->create($address);
    }

    public function setDefaultAddress(Request $request, $id)
    {
        try {

            $user = Auth::guard('api')->user();
            $address = $user->addresses()->where([
                'id' => $id
            ])->first();
            if (!$address) {
                return null;
            }
            DB::beginTransaction();
            $user->addresses()->update([
                'is_default' => false
            ]);
            $address->update([
                'is_default' => true
            ]);
            DB::commit();
            return $address;
        } catch (Exception $exception) {
            DB::rollBack();
            return null;
        }
    }

    public function updateAddress(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();
            $address = $user->addresses()->where([
                'id' => $id
            ])->first();
            if (!$address) {
                return null;
            }
            DB::beginTransaction();
            $object = $this->prepareAddressObj($request);
            $address->update($object);
            DB::commit();
            return $address;
        } catch (Exception $exception) {
            DB::rollBack();
            return null;
        }
    }

    public function updateInfo(Request $request)
    {
        try {

            $user = Auth::guard('api')->user();
            $columns = [
                'first_name' => $request->input('first_name'),
                'middle_name' => $request->input('middle_name'),
                'last_name' => $request->input('last_name'),
            ];
            if ($request->has('country')) {
                $columns['country'] = $request->input('country');
            }
            $user->update($columns);
            return $user;
        } catch (Exception $exception) {
            return null;
        }
    }

    public function updateSecurityInfo(Request $request)
    {
        $email = $request->input('email');
        $user = Auth::guard('api')->user();
        $phone_number = $request->input('phone_number');
        $country_code = $request->input('country_code');
        $check = $this->getByColumns([
            'email' => $email,
            ['id', '!=', $user->id]
        ])->first();
        if ($check) {
            throw  new Exception('Email is already exist');
        }
        $check = $this->getByColumns([
            ['phone_number', 'like', '%' . _OTPHelper::trimPhoneNumber($phone_number)],
            'country_code' => $country_code,
            ['id', '!=', $user->id]
        ])->first();
        if ($check) {
            throw  new Exception('Phone Number is already exist');
        }
        $user->update([
            'email' => $request->input('email'),
            'phone_number' => _OTPHelper::trimPhoneNumber($phone_number),
            'country_code' => $country_code,
        ]);
        return $user;
    }


    public function prepareAddressObj(Request $request): array
    {
        return [
            'country' => $request->input('country'),
            'address' => $request->input('address'),
            'country_code' => $request->input('country_code'),
            'city' => $request->input('city'),
            'building' => $request->input('building'),
            'flat_number' => $request->input('flat_number'),
            'type' => $request->input('type'),
        ];
    }


    public function contact(Request $request)
    {
        $name = $request->input('name');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $msg = $request->input('msg');
        return _EmailHelper::sendEmailToSupport($email, [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'msg' => $msg,
        ], 'emails.contact', 'Customer Inquiry');
    }
}
