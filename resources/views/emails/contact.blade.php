<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Inquiry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .message {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Customer Inquiry</h2>

    <p><strong>Name:</strong> {{$name}}</p>
    <p><strong>Email:</strong> {{$email}}</p>
    <p><strong>Phone:</strong> {{$phone}}</p>

    <div class="message">
        <p><strong>Message:</strong></p>
        <p>{{$msg}}</p>
    </div>
</div>

</body>
</html>
