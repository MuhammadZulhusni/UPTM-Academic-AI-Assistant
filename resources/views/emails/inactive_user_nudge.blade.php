<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f3f4f6; padding: 24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px; width:100%; background-color:#ffffff; border:1px solid #e5e7eb;">
                    <tr>
                        <td align="center" style="padding: 28px 32px 20px; border-bottom: 1px solid #e5e7eb;">
                            @php
                                $logoSrc = $logoSrc ?? (isset($message) ? $message->embed(public_path('upload/uptm.png')) : asset('upload/uptm.png'));
                            @endphp
                            <img src="{{ $logoSrc }}" alt="UPTM" width="88" style="display:block; width:88px; height:auto; margin:0 auto 10px;">
                            <p style="margin:0; font-family: Arial, Helvetica, sans-serif; font-size:15px; font-weight:700; color:#1e3a8a; letter-spacing:0.01em;">
                                UPTM Academic AI Assistant
                            </p>
                            <p style="margin:6px 0 0; font-family: Arial, Helvetica, sans-serif; font-size:12px; color:#6b7280;">
                                University Poly-Tech Malaysia
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 28px 32px 8px; font-family: Arial, Helvetica, sans-serif; color:#111827; font-size:15px; line-height:1.6;">
                            <p style="margin:0 0 16px;">Hello {{ $userName }},</p>
                            @foreach(preg_split('/\r\n|\r|\n/', $bodyText) as $paragraph)
                                @if(trim($paragraph) !== '')
                                    <p style="margin:0 0 14px;">{{ $paragraph }}</p>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 8px 32px 28px;">
                            <span style="display:inline-block; background-color:#1e40af; color:#ffffff; font-family: Arial, Helvetica, sans-serif; font-size:14px; font-weight:700; text-decoration:none; padding:12px 22px;">
                                Sign in
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 16px 32px 24px; border-top: 1px solid #e5e7eb; font-family: Arial, Helvetica, sans-serif; font-size:12px; line-height:1.5; color:#6b7280;">
                            <p style="margin:0 0 6px;">This is an automated reminder from UPTM Academic AI Assistant.</p>
                            <p style="margin:0;">If you were not expecting this email, you can ignore it.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
