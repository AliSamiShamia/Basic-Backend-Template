<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface IUser extends IBase
{
    public function login(Request $request);

    public function loginByEmail(Request $request);

    public function adminLogin(Request $request);

    public function register(Request $request);

    public function address(Request $request);

    public function setDefaultAddress(Request $request, $id);

    public function updateAddress(Request $request, $id);

    public function updateInfo(Request $request);

    public function updateSecurityInfo(Request $request);

    public function sendOtp(Request $request, $user);

    public function checkOtp(Request $request, $user);

    public function getByPhone(Request $request);

    public function contact(Request $request);
}
