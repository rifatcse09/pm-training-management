<!DOCTYPE html>
<html>
<head>
    <title>Account Activated</title>
</head>
<body>
    <p>Dear {{ $name }},</p>
    <p>Your account has been activated. You can now log in using the link below:</p>
    <p><a href="{{ $loginUrl }}">Login Here</a></p>
    <p>Thank you!</p>
</body>
</html>
