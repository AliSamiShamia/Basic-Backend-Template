<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333333;
        }

        p {
            color: #555555;
        }

        .otp {
            font-size: 24px;
            font-weight: bold;
            color: #3498db;
            margin-top: 10px;
        }

        .note {
            color: #888888;
            margin-top: 10px;
        }

        .footer {
            margin-top: 20px;
            color: #888888;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>One-Time Password (OTP) for Verification</h2>
    <p>Hello,</p>
    <p>Your One-Time Password (OTP) for verification is:</p>
    <p class="otp">{{$otp}}</p>
    <p class="note">This OTP is valid for a limited time. Do not share it with anyone.</p>
    <div class="footer">
        <p>Best Regards,<br>JWPharma City Middle East</p>
    </div>
</div>
</body>
</html>
