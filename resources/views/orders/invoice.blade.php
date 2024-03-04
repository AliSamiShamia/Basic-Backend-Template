<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>JWPharma Invoice- {{$order->tracking_number}}</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .invoice {
            width: 100%;
            margin: 1px auto;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            color: #333;
        }

        .invoice-header {
            padding: 20px;
            width: 100%;
        }

        .invoice-number {
            font-size: 1rem;
            color: #3a3541aa;
            margin: 0px;
            letter-spacing: 0.15px;
            font-family: Inter, sans-serif, -apple-system, BlinkMacSystemFont,
            "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif,
            "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            font-weight: 500;
            line-height: 1.6;
        }

        .invoice-date {
            font-size: 0.875rem;
            text-align: end;
            width: 100%;
            color: #3a354199;
            margin: 0px;
            line-height: 1.5;
            letter-spacing: 0.15px;
            font-family: Inter, sans-serif, -apple-system, BlinkMacSystemFont,
            "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif,
            "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            font-weight: 400;
        }

        .invoice-details {
            padding: 20px;
        }

        .invoice-from,
        .invoice-to {
            /*width: 48%;*/
        }

        .invoice-from h2,
        .invoice-to h2 {
            color: #3498db;
            font-size: 15px;
        }

        .invoice-from p,
        .invoice-to p {
            font-size: 13px;
        }

        .invoice-items {
            margin-top: 20px;
            border-collapse: collapse;
            width: 100%;
        }

        .invoice-items th,
        .invoice-items td {
            border: 1px solid #ddd;
            border-right: none;
            border-left: none;
            padding: 12px;
            text-align: left;
            font-size: 14px;
            color: #333;
        }

        .invoice-total {
            margin-top: 20px;
            text-align: right;
            padding: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>
<div class="invoice">
    <table class="invoice-header">

        <tbody>
        <tr>
            <td style="width: 50%;">
                <img
                    src="{{asset('logo.png')}}"
                    style="width: 100px;"
                    alt="Logo"/>
            </td>
            <td style="text-align: end;width: 50%;">
                <div class="invoice-date">
                    Invoice &nbsp;&nbsp;&nbsp;&nbsp;<b> #{{$order->tracking_number}}</b><br/>
                    Date Issued:&nbsp;&nbsp;&nbsp;<b>{{$order->created_at}}</b>
                </div>
            </td>
        </tr>
        </tbody>

    </table>
    <hr
        style="
          margin: 0px;
          flex-shrink: 0;
          border-width: 0px 0px thin;
          border-style: solid;
          border-color: #3a35411f;
        "
    />

    <table class="invoice-items">
        <tbody>
        <tr style="">
            <td style="width: 50%;">
                <div class="invoice-to" style="width: 100%;">

                    <h2>Billed To:</h2>
                    <p>
                        {{$order->UserAddress->address}}<br/>
                        {{$order->UserAddress->city.", ".$order->UserAddress->country}}<br/>
                        @if($order->User->phone_number)
                            Phone:
                            {{$order->User->country_code."".$order->User->phone_number}}<br/>
                        @endif
                    </p>
                </div>

            </td>
            <td style="width: 50%;">
                <div class="invoice-to" style="width: 100%;">

                    <h2>Invoice To:</h2>
                    <p>
                        Client Name:{{$order->User->first_name." ".$order->User->last_name}}<br/>
                        @if($order->User->email)
                            Email: {{$order->User->email}}<br/>
                        @endif
                    </p>
                </div>
            </td>
        </tr>

        </tbody>
    </table>
    <hr
        style="
          margin: 0px;
          flex-shrink: 0;
          border-width: 0px 0px thin;
          border-style: solid;
          border-color: #3a35411f;
        "
    />

    <table class="invoice-items">
        <thead>
        <tr>
            <th>Description</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->OrderItems()->get() as $item)
            <tr style="text-align: center">
                <td >
                    {{$item->Product->name}}
                    <br/>
                    @if(count($item->OrderItemParam()->get())>0)
                        <ul>
                            @foreach($item->OrderItemParam()->get() as $param)
                                <li>{{$param->value}}</li>
                            @endforeach
                        </ul>
                    @endif
                </td>
                <td>{{$item->quantity}}</td>
                <td>${{$item->price}}</td>
                <td>${{$item->quantity*$item->price}}</td>
            </tr>
        @endforeach

        <!-- Add more rows for additional items -->
        </tbody>
    </table>

    <div class="invoice-total">
        <p>Total: ${{$order->total_amount}}</p>
    </div>
</div>
</body>
</html>
