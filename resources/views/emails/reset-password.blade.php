<!DOCTYPE html>
<html>
<head>
    <title>Password Reset - Payrix</title>
    <meta charset="utf-8">
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">

    <!-- Main Wrapper -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600"
                       style="background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">

                    <!-- Hero Section -->
                    <tr>
                        <td style="background: #28a745; padding: 40px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: bold;">
                                Reset Your Password
                            </h1>
                            <p style="color: #e6ffe6; font-size: 15px; margin: 10px 0 0;">
                                Hi {{ $user->name ?? 'User' }}, let’s secure your account.
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px; text-align: left;">
                            <p style="font-size: 16px; color: #555; line-height: 1.6;">
                                We received a request to reset your password for your <b>Payrix</b> account.
                                Click the button below to choose a new password.
                            </p>

                            <!-- Call to Action -->
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{ $url }}"
                                   style="background: #28a745; color: #ffffff; text-decoration: none;
                                          padding: 12px 25px; border-radius: 6px;
                                          font-size: 16px; font-weight: bold; display: inline-block;">
                                   Reset Password
                                </a>
                            </div>

                            <p style="font-size: 15px; color: #555; line-height: 1.6;">
                                🔒 For security reasons, this link will expire in <b>60 minutes</b>.
                                If you don’t reset your password within this time, you’ll need to request a new link.
                            </p>

                            <p style="font-size: 15px; color: #555; line-height: 1.6;">
                                If you did not request a password reset, please ignore this email.
                                Your account will remain secure.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: #f8f9fa; padding: 20px; text-align: center;">
                            <p style="font-size: 12px; color: #aaa; margin-top: 15px;">
                                © {{ date('Y') }} Payrix . All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
