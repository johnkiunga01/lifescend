<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Outfit, Arial, sans-serif; background: #f8fafc; padding: 32px;">
    <div style="max-width: 560px; margin: 0 auto; background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <div style="text-align: center; margin-bottom: 24px;">
            <span style="display: inline-block; background: #D4AF37; color: white; width: 40px; height: 40px; border-radius: 10px; line-height: 40px; font-size: 20px; font-weight: bold;">L</span>
        </div>

        <h1 style="font-size: 20px; color: #0f172a; margin: 0 0 8px;">Training Enquiry Response</h1>
        <p style="color: #64748b; font-size: 14px; margin: 0 0 20px;">Dear <strong>{{ $enquiry->name }}</strong>,</p>

        <p style="color: #334155; font-size: 14px; line-height: 1.6; margin: 0 0 20px;">
            Thank you for your enquiry regarding <strong>{{ $enquiry->event->name ?? 'our training programs' }}</strong>.
        </p>

        <div style="background: #f1f5f9; border-radius: 12px; padding: 20px; margin: 0 0 20px;">
            <p style="color: #334155; font-size: 14px; line-height: 1.6; margin: 0; white-space: pre-wrap;">{{ $replyMessage }}</p>
        </div>

        <p style="color: #64748b; font-size: 13px; margin: 0 0 4px;">Best regards,</p>
        <p style="color: #0f172a; font-size: 14px; font-weight: 600; margin: 0;">Lifescend Team</p>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0 16px;">
        <p style="color: #94a3b8; font-size: 11px; margin: 0; text-align: center;">This email was sent in response to your training enquiry.</p>
    </div>
</body>
</html>
