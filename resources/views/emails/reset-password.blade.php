<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body>
    <p>Hello {{ $user->name ?? 'User' }},</p>

    <p>You requested to reset your password. Click the link below to proceed:</p>

    <p><a href="{{ $resetUrl }}">Reset Password</a></p>

    <p>If you didn't request this, you can safely ignore this email.</p>

    <p>Thanks,<br>Your App Team</p>
</body>
</html>
