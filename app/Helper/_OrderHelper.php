<?php

namespace App\Helper;

use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class _OrderHelper
{

    public static function invoice($order)
    {
        try {
            $html = View::make('orders.invoice', compact('order'))->render();
            $path = public_path('invoices');
            $file_name = md5($order->tracking_number . "-order-" . $order->id) . '.pdf';
            File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            $res = PDF::loadHTML($html)->setOption([
                'dpi' => 82, 'defaultFont' => 'sans-serif'
            ])->setPaper('a4')->setWarnings(false)->save($path . '/' . $file_name);
            return asset("invoices/" . $file_name);
        } catch (Exception $exception) {
            Log::error($exception);
            return null;
        }
    }

}
