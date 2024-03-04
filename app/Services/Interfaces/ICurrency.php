<?php

namespace App\Services\Interfaces;

interface ICurrency extends IBase
{

    public function ChangeMainCurrency($id);

    //Default Currency for user
    public function switchCurrency($currency_id);

}
