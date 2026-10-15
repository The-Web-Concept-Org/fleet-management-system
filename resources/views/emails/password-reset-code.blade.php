<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password reset code</title>
</head>
<body style="margin:0;padding:0;background-color:#f2f3ff;font-family:Arial,Helvetica,sans-serif;color:#131b2e;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f2f3ff;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(19,27,46,0.08);">
                    <tr>
                        <td style="background-color:#BD1C23;padding:24px 32px;color:#ffffff;">
                            <h1 style="margin:0;font-size:20px;font-weight:700;">{{ $appName }}</h1>
                            <p style="margin:4px 0 0;font-size:13px;opacity:0.9;">Password reset requested</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 16px;font-size:15px;">Hello <strong>{{ $driverName }}</strong>,</p>
                            <p style="margin:0 0 24px;font-size:14px;line-height:1.6;color:#424655;">
                                Use the code below to reset the password for <strong>{{ $loginEmail }}</strong>.
                                The code expires in {{ $expiresInMinutes }} minutes.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f2f3ff;border-radius:12px;">
                                <tr>
                                    <td align="center" style="padding:24px;">
                                        <span style="display:inline-block;font-family:Consolas,monospace;font-size:32px;font-weight:700;letter-spacing:8px;color:#131b2e;">{{ $code }}</span>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:24px 0 0;font-size:12px;line-height:1.6;color:#6b7280;">
                                If you did not request a password reset, you can safely ignore this email.
                                Never share this code with anyone.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px;background-color:#f2f3ff;font-size:11px;color:#6b7280;text-align:center;">
                            {{ $appName }} &bull; Automated message, please do not reply.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
