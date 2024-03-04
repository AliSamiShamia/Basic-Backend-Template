<?php

namespace App\Services\Facades;

use App\Helper\_RuleHelper;
use App\Models\ParamType;
use App\Services\Interfaces\IType;

class FType extends FBase implements IType
{
    public function __construct()
    {
        $this->model = ParamType::class;
        $this->rules = [
            'param' => _RuleHelper::_Rule_Require,
        ];
        $this->columns = ['param'];
    }
}
