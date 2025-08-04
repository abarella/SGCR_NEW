<!DOCTYPE html>
<html>
<head>
    <title>Setup 2FA</title>
</head>
<body>
    <h2>Setup 2FA</h2>
    <p>Scan this QR code with your Google Authenticator app.</p>
    <img src="{{ $QR_Image }}" alt="QR Code">
    <p>Or enter this code manually: <strong>{{ $secret }}</strong></p>
    <form method="POST" action="{{ route('2fa.postSetup') }}">
        @csrf
        <input type="hidden" name="secret" value="{{ $secret }}">
        <label for="code">Enter the code from the app:</label>
        <input type="text" name="code" id="code" required>
        <button type="submit">Enable 2FA</button>
    </form>
</body>
</html>
