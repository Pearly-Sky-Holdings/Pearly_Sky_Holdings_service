<!-- resources/views/emails/reset_password.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Your Password</h1>
    <p>You've requested to reset your password.</p>
    <p>Your OTP code is: <strong>{{ $otp }}</strong></p>
    <p>This code will expire in 15 minutes.</p>
    <p>If you did not request a password reset, please ignore this email.</p>
</body>
</html>