<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #0d6efd;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background-color: #0d6efd;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>EVSU Research Archive</h1>
        </div>
        
        <div class="content">
            <h2>Verify Your Email Address</h2>
            
            <p>Hello <?= $name ?>,</p>
            
            <p>Thank you for registering with EVSU Research Archive. To complete your registration and access all features, please verify your email address by clicking the button below:</p>
            
            <div style="text-align: center;">
                <a href="<?= $verification_link ?>" class="button">Verify Email Address</a>
            </div>
            
            <p>If you did not create an account, no further action is required.</p>
            
            <p>If you're having trouble clicking the button, copy and paste the following URL into your web browser:</p>
            <p style="word-break: break-all;"><?= $verification_link ?></p>
            
            <p>This verification link will expire in 24 hours.</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
            <p>&copy; <?= date('Y') ?> EVSU Research Archive. All rights reserved.</p>
        </div>
    </div>
</body>
</html> 