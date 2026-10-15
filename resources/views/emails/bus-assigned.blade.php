<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus assigned to you</title>
</head>
<body style="margin:0;padding:0;background-color:#f2f3ff;font-family:Arial,Helvetica,sans-serif;color:#131b2e;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f2f3ff;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(19,27,46,0.08);">
                    <tr>
                        <td style="background-color:#BD1C23;padding:24px 32px;color:#ffffff;">
                            <h1 style="margin:0;font-size:20px;font-weight:700;">Bus Assignment</h1>
                            <p style="margin:4px 0 0;font-size:13px;opacity:0.9;">Bus #{{ $busNumber }} assigned to you</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 16px;font-size:15px;">Hello <strong>{{ $driverName }}</strong>,</p>
                            <p style="margin:0 0 24px;font-size:14px;line-height:1.6;color:#424655;">
                                You have been assigned the following vehicle. Please review the details before your next shift.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f2f3ff;border-radius:12px;padding:20px;">
                                <tr>
                                    <td style="padding:6px 0;font-size:13px;color:#424655;">Bus number</td>
                                    <td style="padding:6px 0;font-size:14px;font-weight:700;text-align:right;">Bus #{{ $busNumber }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;font-size:13px;color:#424655;">License plate</td>
                                    <td style="padding:6px 0;font-size:14px;font-weight:700;text-align:right;font-family:Consolas,monospace;">{{ $busPlate }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;font-size:13px;color:#424655;">Model</td>
                                    <td style="padding:6px 0;font-size:14px;font-weight:700;text-align:right;">{{ $busModel ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;font-size:13px;color:#424655;">Assigned route</td>
                                    <td style="padding:6px 0;font-size:14px;font-weight:700;text-align:right;">{{ $busRoute ?: 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;font-size:13px;color:#424655;">Fleet status</td>
                                    <td style="padding:6px 0;font-size:14px;font-weight:700;text-align:right;">{{ ucfirst(str_replace('_', ' ', $busStatus)) }}</td>
                                </tr>
                            </table>

                            <p style="margin:24px 0 8px;font-size:13px;color:#424655;">
                                You can sign in to view your schedule with your existing driver credentials:
                            </p>
                            <p style="margin:0 0 8px;font-size:14px;font-weight:700;">{{ $loginEmail }}</p>

                            <p style="margin:24px 0;text-align:center;">
                                <a href="{{ $loginUrl }}" style="display:inline-block;background-color:#BD1C23;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;padding:14px 28px;border-radius:10px;">Open Fleet Portal</a>
                            </p>

                            <p style="margin:0;font-size:12px;line-height:1.6;color:#6b7280;">
                                Forgot your password? Contact your dispatch coordinator to have it reset.
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
