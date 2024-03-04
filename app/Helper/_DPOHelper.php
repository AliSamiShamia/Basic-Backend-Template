<?php

namespace App\Helper;

use Exception;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Illuminate\Http\Request;

class _DPOHelper
{
    public static function CreateToken($user, $order_id, $amount)
    {

        try {
            $token = _EmailHelper::generateTransactionToken($user);
            $num = str_pad($order_id, 5, '0', STR_PAD_LEFT);
            $endpoint = getenv('DPO_URL');
            $companyToken = getenv('xxxxx');
            $serviceType = getenv('xxxxx');
            $redirectURL = getenv('xxxxx') . "/" . $order_id . "/" . $token;
            $backURL = getenv('xxxxx');
            $address = $user->addresses()->where('is_default', true)->first();
            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><API3G></API3G>');
            $xml->addChild('CompanyToken', $companyToken);
            $xml->addChild('Request', 'createToken');

            $transaction = $xml->addChild('Transaction');
            $transaction->addChild('PaymentAmount', $amount);
            $transaction->addChild('PaymentCurrency', 'USD');
            $transaction->addChild('RedirectURL', $redirectURL);
            $transaction->addChild('BackURL', $backURL);
            $transaction->addChild('CompanyRefUnique', '0');
            $transaction->addChild('PTL', '5');

            $services = $xml->addChild('Services');
            $service = $services->addChild('Service');
            $service->addChild('ServiceType', $serviceType);
            $service->addChild('ServiceDescription', 'xxxxx');
            $service->addChild('ServiceDate', date('Y/m/d H:i'));

            if ($user->email) {
                $transaction->addChild('customerEmail', $user->email);
            }
            if ($user->first_name) {
                $transaction->addChild('customerFirstName', $user->first_name);
            }
            if ($user->last_name) {
                $transaction->addChild('customerLastName', $user->last_name);
            }
            if ($address) {
                $transaction->addChild('customerAddress', $address->building . ", " . $address->flat_number);
                $transaction->addChild('customerCity', $address->city);
                $transaction->addChild('customerCountry', $address->country_code);
            }
            if ($user->phone_number && $user->country_code) {
                $transaction->addChild('customerPhone', $user->country_code . $user->phone_number);
            }

            $formattedXml = $xml->asXML();


            [$result, $httpCode] = self::callAPi($endpoint, $formattedXml);
            $payment_url = null;
            if ($httpCode == 200) {
                $xml = simplexml_load_string($result);
                if ($xml !== false) {
                    $response = json_decode(json_encode($xml), FALSE);//collect($xml)->toArray();
                    $res = $response->Result;
                    if ($res == '000') {
                        $transToken = $xml->TransToken;
                        $payment_url = getenv('DPO_Payment_URL') . $transToken;
                    } else {
                        $explanation = $xml->ResultExplanation;
                        Log::error($explanation);
                        return null;

                    }
                }
            } else {
                Log::error($result);
            }
            return $payment_url;
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }


    public static function verifyToken(Request $request)
    {
        try {
            $transactionToken = $request->input('TransactionToken');

            $endpoint = getenv('xxxxx');
            $companyToken = getenv('xxxxx');

            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><API3G></API3G>');
            $xml->addChild('CompanyToken', $companyToken);
            $xml->addChild('Request', 'verifyToken');
            $xml->addChild('TransactionToken', $transactionToken);
            $formattedXml = $xml->asXML();
            [$result, $httpCode] = self::callAPi($endpoint, $formattedXml);
            if ($httpCode == 200) {
                $xml = simplexml_load_string($result);
                if ($xml !== false) {
                    $response = json_decode(json_encode($xml), FALSE);//collect($xml)->toArray();
                    $res = $response->Result;
                    if ($res == '000') {
                        return true;
                    } else {
                        $explanation = $xml->ResultExplanation;
                        Log::error($explanation);
                        return null;
                    }
                }
            }
            return true;
        } catch (Exception $exception) {
            return null;
        }
    }

    public static function callAPi($endpoint, $formattedXml)
    {
        try {

            $ch = curl_init();

            if (!$ch) {
                die("Couldn't initialize a cURL handle");
            }
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $formattedXml);

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return [$result, $httpCode];
        } catch (Exception $exception) {
            return [null, null];
        }
    }

}
