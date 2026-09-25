<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Account Support Request</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 0 auto; padding: 40px 20px; }
        .card { background: #ffffff; border-radius: 16px; padding: 40px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .logo { width: 48px; height: 48px; background: #D4AF37; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 800; color: #fff; }
        h1 { font-size: 22px; font-weight: 800; color: #0f172a; margin: 24px 0 8px; }
        p { font-size: 15px; color: #475569; line-height: 1.6; margin: 0 0 8px; }
        .label { font-weight: 600; color: #0f172a; }
        .box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin: 16px 0; }
        .btn { display: inline-block; background: #D4AF37; color: #ffffff; font-weight: 700; font-size: 15px; padding: 14px 32px; border-radius: 12px; text-decoration: none; margin: 16px 0; }
        .footer { margin-top: 32px; padding-top: 24px; border-top: 1px solid #e2e8f0; font-size: 13px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="logo">L</div>
            <h1>Account Support Request</h1>
            <p>A user has submitted an account support request.</p>
            <div class="box">
                <p><span class="label">Name:</span> {{ $supportRequest->name ?? 'Not provided' }}</p>
                <p><span class="label">Email:</span> {{ $supportRequest->email }}</p>
                <p><span class="label">Submitted:</span> {{ $supportRequest->created_at->format('M d, Y H:i') }}</p>
                <p style="margin-top: 12px;"><span class="label">Message:</span></p>
                <p>{{ $supportRequest->message }}</p>
            </div>
            <a href="{{ url('/manage/account-support') }}" class="btn">View in Dashboard</a>
            <div class="footer">
                <p>&copy; {{ date('Y') }} Lifescend. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>