<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 0 auto; padding: 40px 20px; }
        .card { background: #ffffff; border-radius: 16px; padding: 40px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .logo { width: 48px; height: 48px; background: #D4AF37; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 800; color: #fff; }
        h1 { font-size: 22px; font-weight: 800; color: #0f172a; margin: 24px 0 8px; }
        p { font-size: 15px; color: #475569; line-height: 1.6; margin: 0 0 16px; }
        .btn { display: inline-block; background: #D4AF37; color: #ffffff; font-weight: 700; font-size: 15px; padding: 14px 32px; border-radius: 12px; text-decoration: none; margin: 16px 0; }
        .btn:hover { background: #B8960F; }
        .footer { margin-top: 32px; padding-top: 24px; border-top: 1px solid #e2e8f0; font-size: 13px; color: #94a3b8; }
        .footer a { color: #D4AF37; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="logo">L</div>
            <h1>Verify Your Email Address</h1>
            <p>Thanks for creating an account with Lifescend! Please click the button below to verify your email address and get started.</p>
            <a href="{{ $verificationUrl }}" class="btn">Verify Email Address</a>
            <p>If you didn't create this account, no further action is required.</p>
            <div class="footer">
                <p>If you're having trouble clicking the button, copy and paste this URL into your browser:</p>
                <p style="word-break: break-all; font-size: 13px; color: #64748b;">{{ $verificationUrl }}</p>
                <p>&copy; {{ date('Y') }} Lifescend. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>