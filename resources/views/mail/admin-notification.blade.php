<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectText }}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 24px 16px; }
        .card { background: #ffffff; border-radius: 16px; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
        .header { text-align: center; margin-bottom: 24px; }
        .header h1 { font-size: 20px; color: #0f172a; margin: 0; font-weight: 800; }
        .divider { height: 1px; background: #e2e8f0; margin: 20px 0; }
        .body-text { font-size: 15px; line-height: 1.6; color: #334155; white-space: pre-wrap; }
        .btn { display: inline-block; margin-top: 20px; padding: 12px 24px; background: #D4AF37; color: #ffffff; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 14px; }
        .footer { text-align: center; margin-top: 24px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h1>{{ $subjectText }}</h1>
            </div>
            <div class="divider"></div>
            <div class="body-text">{{ $body }}</div>
            @if($actionUrl && $actionText)
            <div style="text-align: center;">
                <a href="{{ $actionUrl }}" class="btn">{{ $actionText }}</a>
            </div>
            @endif
            <div class="divider"></div>
            <div class="footer">
                <p>This is an automated notification from your Lifescend platform.</p>
            </div>
        </div>
    </div>
</body>
</html>
