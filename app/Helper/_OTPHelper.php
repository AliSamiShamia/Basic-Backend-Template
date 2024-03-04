<?php

namespace App\Helper;

use App\Models\User;
use Brevo\Client\Api\AccountApi;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\ApiException;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Exception;

class _OTPHelper
{

    public static TransactionalEmailsApi $api_instance;

    public function __construct()
    {

    }

    public static function send($phoneNumber, $otp)
    {
        try {

            $endpoint = getenv('xxxxx');
            $ch = curl_init();
            if (!$ch) {
                Log::error("Couldn't initialize a cURL handle");
                return null;
            }
            $headers = array(
                'Content-Type: application/json',
                'accept: application/json',
                'Api-Key: ' . getenv('xxxxx'),
            );
            $data = array(
                "contactNumbers" => [$phoneNumber],
                "templateId" => xxxxx,
                "senderNumber" => "xxxxx"
            );

            //..........Send opt to the phone number using SMS api.....
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $result = curl_exec($ch);
            curl_close($ch);

            //..........then return the response if it is correct or wrong
//            Log::error($otp);
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function sendToMail($email, $otp)
    {
//        _EmailHelper::sendOTPByEmail($email, $otp, 'emails.otp', 'OTP Verifications');
        $sendSmtpEmail = new SendSmtpEmail([
            'subject' => 'OTP Verification!',
            'sender' => ['name' => 'xxxxx', 'email' => 'xxxxx@gmail.com'],
            'replyTo' => ['name' => 'xxxxx', 'email' => 'xxxxx@gmail.com'],
            'to' => [['email' => $email]],
            'htmlContent' => view('emails.otp', ['otp' => $otp])->render(),
            'params' => ['otp' => $otp]
        ]);
        try {
            $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', getenv('xxxxx'));
            $api_instance = new TransactionalEmailsApi(
                new Client(),
                $config
            );
            $api_instance->sendTransacEmail($sendSmtpEmail);
        } catch (ApiException $e) {
            Log::error('Exception when calling TransactionalEmailsApi->sendTransacEmail:' . $e->getMessage());
            return false;
        }
        return true;
    }

    public static function preparePhoneNumber($phone, $code)
    {
        //it is useful to remove the first 0 from the phone number if exist
        $phone_umber_without_zero = self::trimPhoneNumber($phone);
        return $code . $phone_umber_without_zero;
    }

    public static function trimPhoneNumber($phone)
    {
        return ltrim($phone, '0');
    }

    public static function generateOtp()
    {
        return rand(100000, 999999);
    }
}
