<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Outfit, Arial, sans-serif; background: #f8fafc; padding: 32px;">
    <div style="max-width: 560px; margin: 0 auto; background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <div style="text-align: center; margin-bottom: 24px;">
            <span style="display: inline-block; background: #D4AF37; color: white; width: 40px; height: 40px; border-radius: 10px; line-height: 40px; font-size: 20px; font-weight: bold;">L</span>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            <div style="display: inline-block; background: #d1fae5; width: 56px; height: 56px; border-radius: 50%; line-height: 56px; font-size: 28px;">&#10003;</div>
        </div>

        <h1 style="font-size: 20px; color: #0f172a; margin: 0 0 8px; text-align: center;">Training Request Approved</h1>
        <p style="color: #64748b; font-size: 14px; margin: 0 0 20px; text-align: center;">Dear <strong>{{ $trainingRequest->user->name }}</strong>,</p>

        <p style="color: #334155; font-size: 14px; line-height: 1.6; margin: 0 0 16px;">
            Great news! Your training request for <strong>{{ $trainingRequest->title }}</strong> has been approved.
        </p>

        <div style="background: #f1f5f9; border-radius: 12px; padding: 20px; margin: 0 0 20px;">
            <p style="color: #475569; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 8px;">Request Details</p>
            <table style="width: 100%; font-size: 13px; color: #334155;">
                <tr><td style="padding: 4px 0; color: #64748b;">Format</td><td style="padding: 4px 0; font-weight: 600;">{{ ucfirst($trainingRequest->training_type) }}</td></tr>
                <tr><td style="padding: 4px 0; color: #64748b;">Preferred Date</td><td style="padding: 4px 0; font-weight: 600;">{{ $trainingRequest->preferred_date->format('F d, Y') }}</td></tr>
                @if($trainingRequest->estimated_attendees)
                <tr><td style="padding: 4px 0; color: #64748b;">Attendees</td><td style="padding: 4px 0; font-weight: 600;">{{ $trainingRequest->estimated_attendees }}</td></tr>
                @endif
            </table>
        </div>

        <div style="background: #fef3c7; border-radius: 12px; padding: 20px; margin: 0 0 20px; border-left: 4px solid #D4AF37;">
            <p style="color: #92400e; font-size: 13px; line-height: 1.6; margin: 0; white-space: pre-wrap;">{{ $customMessage }}</p>
        </div>

        <p style="color: #64748b; font-size: 13px; margin: 0 0 4px;">Best regards,</p>
        <p style="color: #0f172a; font-size: 14px; font-weight: 600; margin: 0;">Lifescend Team</p>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0 16px;">
        <p style="color: #94a3b8; font-size: 11px; margin: 0; text-align: center;">This email confirms your training request has been approved. We'll be in touch with next steps.</p>
    </div>
</body>
</html>
