<?php

namespace App\Services\Facades;

use App\Helper\_RuleHelper;
use App\Models\Currency;
use App\Models\UserCurrency;
use App\Services\Interfaces\IBase;
use App\Services\Interfaces\ICurrency;
use Illuminate\Support\Facades\Auth;

class FCurrency extends FBase implements ICurrency
{

    public function __construct()
    {
        $this->model = Currency::class;
        $this->rules = [
            'code' => _RuleHelper::_Rule_Require,
            'name' => _RuleHelper::_Rule_Require,
            'symbol' => _RuleHelper::_Rule_Require,
            'rate' => _RuleHelper::_Rule_Require,
        ];
        $this->columns = ['code', 'name', 'symbol', 'rate'];
    }


    public function ChangeMainCurrency($id): int
    {
        Currency::query()->update([
            'is_main' => false
        ]);
        return Currency::query()->where(['id' => $id])->update([
            'is_main' => true
        ]);
    }

    public function switchCurrency($currency_id)
    {
        $currency = $this->getById($currency_id);
        if ($currency) {
            $check = UserCurrency::query()->where([
                'user_id' => Auth::guard('api')->id()
            ])->first();
            if ($check) {
                $check->update([
                    'currency_id' => $currency->id
                ]);
            } else {
                UserCurrency::query()->create([
                    'user_id' => Auth::guard('api')->id(),
                    'currency_id' => $currency->id
                ]);
            }
        }
        return null;
    }
}
