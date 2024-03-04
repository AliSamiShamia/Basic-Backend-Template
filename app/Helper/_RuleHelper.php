<?php


namespace App\Helper;


class _RuleHelper
{
    const _Rule_Require = 'required';
    const _Rule_Require_without = 'required_without:';
    const _Rule_Email = 'email';
    const _Rule_Number = 'numeric';
    const _Rule_Date = 'date_format:Y-m-d';
    const _Rule_Date_Time = 'date_format:Y-m-d H:i:s';
    const _Rule_Time = 'date_format:H:i:s';
    const _Rule_After_Time = 'date_format:H:i:s|after:';
    const _Rule_Min = 'min:';
    const _Rule_Max = 'max:';

    const _RULE_LIST = [
        'login' => [
            'phone_number' => self::_Rule_Require,
            'country_code' => self::_Rule_Require,
        ],
        'loginByPhone' => [
            'phone_number' => self::_Rule_Require,
        ],
        'loginByEmail' => [
            'email' => [
                self::_Rule_Require,
                self::_Rule_Email,
            ],
        ],
        'otp' => [
            'user_name' => self::_Rule_Require,
            'country_code' => self::_Rule_Require,
        ],
        'otp_for_email' => [
            'user_name' => self::_Rule_Require,
            'user_id' => self::_Rule_Require
        ],
        'otp_for_phone' => [
            'user_name' => self::_Rule_Require,
            'country_code' => self::_Rule_Require,
            'user_id' => self::_Rule_Require
        ],

        'check_otp' => [
            'user_name' => self::_Rule_Require,
            'otp' => self::_Rule_Require,
            'user_id' => self::_Rule_Require,
        ],
        'registerByPhone' => [
            'user_name' => self::_Rule_Require,
            'country_code' => self::_Rule_Require,
        ],
        'registerByEmail' => [
            'user_name' => self::_Rule_Require . "|" . self::_Rule_Email,
        ],
        'complete_registration' => [
            'first_name' => self::_Rule_Require,
            'last_name' => self::_Rule_Require,
            'country' => self::_Rule_Require,
        ],
        'user-info' => [
            'first_name' => self::_Rule_Require,
            'last_name' => self::_Rule_Require,
        ],
        'security-info' => [
            'email' => self::_Rule_Require,
            'phone_number' => self::_Rule_Require,
            'country_code' => self::_Rule_Require,
        ],
        'add_to_cart' => [
            'product_id' => self::_Rule_Require,
            'quantity' => [
                self::_Rule_Require,
                self::_Rule_Number,
                self::_Rule_Min . '1',
            ],
        ],
        'address' => [
            'country' => self::_Rule_Require,
            'city' => self::_Rule_Require,
            'building' => self::_Rule_Require,
            'flat_number' => self::_Rule_Require,
            'address' => self::_Rule_Require,
            'type' => self::_Rule_Require,
        ],
        'add_to_cart_product_options' => [
            'options' => self::_Rule_Require
        ],
        'update_quantity' => [
            'quantity' => [
                self::_Rule_Require,
                self::_Rule_Number,
                self::_Rule_Min . '1',
            ]
        ],
        'product-create' => [
            'name' => self::_Rule_Require,
            'price' => [
                self::_Rule_Require,
                self::_Rule_Number,
                self::_Rule_Min . '0'
            ],
        ],
        "store_order" => [
            "user_address_id" => self::_Rule_Require,
        ],
        "product_param" => [
            'value' => self::_Rule_Require,
            'type_id' => self::_Rule_Require,
        ],
        "param_type" => [
            'param' => self::_Rule_Require,
        ],
        "getRule" => [
            'files' => self::_Rule_Require,
        ],
        'media'=>[
            'files'=>self::_Rule_Require,
        ],
    ];

    public static function getRule($key)
    {
        return self::_RULE_LIST[$key];
    }
}
