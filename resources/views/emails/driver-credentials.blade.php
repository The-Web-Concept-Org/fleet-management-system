<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Lancotri Driver Account</title>
</head>
<body style="margin:0;padding:0;background-color:#f2f3ff;font-family:Arial,Helvetica,sans-serif;color:#131b2e;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f2f3ff;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(19,27,46,0.08);">
                    <tr>
                        <td style="background-color:#BD1C23;padding:24px 32px;color:#ffffff;">
                            <h1 style="margin:0;font-size:20px;font-weight:700;">Lancotri</h1>
                            <p style="margin:4px 0 0;font-size:13px;opacity:0.9;">Driver account created</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 16px;font-size:15px;">Hello <strong>{{ $driverName }}</strong>,</p>
                            <p style="margin:0 0 24px;font-size:14px;line-height:1.6;color:#424655;">
                                A driver account has been created for you on the Lancotri fleet portal. Use the details below to sign in.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f2f3ff;border-radius:12px;padding:20px;">
                                <tr>
                                    <td style="padding:6px 0;font-size:13px;color:#424655;">Badge / ID</td>
                                    <td style="padding:6px 0;font-size:14px;font-weight:700;text-align:right;">{{ $badgeId ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;font-size:13px;color:#424655;">Login email</td>
                                    <td style="padding:6px 0;font-size:14px;font-weight:700;text-align:right;">{{ $loginEmail }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;font-size:13px;color:#424655;">Temporary password</td>
                                    <td style="padding:6px 0;font-size:14px;font-weight:700;text-align:right;font-family:Consolas,monospace;">{{ $loginPassword }}</td>
                                </tr>
                            </table>

                            <p style="margin:24px 0;text-align:center;">
                                <a href="{{ $loginUrl }}" style="display:inline-block;background-color:#BD1C23;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;padding:14px 28px;border-radius:10px;">Sign in to your account</a>
                            </p>

                            <p style="margin:0;font-size:12px;line-height:1.6;color:#6b7280;">
                                For your security, please change this password after your first login. If you were not expecting this email, contact your dispatch coordinator.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px;background-color:#f2f3ff;font-size:11px;color:#6b7280;text-align:center;">
                            {{ config('app.name') }} &bull; Automated message, please do not reply.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
