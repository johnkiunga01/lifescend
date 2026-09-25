<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Verification Code</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; margin: 0; padding: 0; }
        .wrapper { max-width: 480px; margin: 0 auto; padding: 40px 20px; }
        .card { background: #ffffff; border-radius: 16px; padding: 40px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; text-align: center; }
        .logo { width: 48px; height: 48px; background: #D4AF37; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 800; color: #fff; margin: 0 auto 24px; }
        h1 { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 8px; }
        p { font-size: 15px; color: #475569; line-height: 1.6; margin: 0 0 16px; }
        .otp { font-size: 36px; font-weight: 800; color: #D4AF37; letter-spacing: 8px; margin: 24px 0; padding: 16px; background: #FFFDF0; border-radius: 12px; border: 1px solid #FFEE9E; }
        .footer { margin-top: 32px; padding-top: 24px; border-top: 1px solid #e2e8f0; font-size: 13px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="logo">L</div>
            <h1>Email Verification</h1>
            <p>Use the code below to verify your email address. This code expires in 10 minutes.</p>
            <div class="otp">{{ $otp }}</div>
            <p>If you didn't request this code, no further action is required.</p>
            <div class="footer">
                <p>&copy; {{ date('Y') }} Lifescend. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>