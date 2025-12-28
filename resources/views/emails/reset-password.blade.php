<!DOCTYPE html>
<html>

<head>
    <title>Reset Password - DocDot</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 40px;
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo span {
            font-size: 36px;
            font-weight: bold;
            background: linear-gradient(to right, #F4AFE9, #8DD0FC);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .icon-container {
            text-align: center;
            margin: 30px 0;
        }

        .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #F4AFE9 0%, #8DD0FC 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .icon svg {
            width: 40px;
            height: 40px;
            fill: white;
        }

        .title {
            color: #1b1b18;
            text-align: center;
            font-size: 24px;
            margin: 20px 0 10px;
            font-weight: bold;
        }

        .subtitle {
            color: #666;
            text-align: center;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .message {
            color: #333;
            font-size: 15px;
            margin: 15px 0;
            line-height: 1.8;
        }

        .button-container {
            text-align: center;
            margin: 35px 0;
        }

        .button {
            display: inline-block;
            background: linear-gradient(to right, #F4AFE9, #8DD0FC);
            color: #1b1b18 !important;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(141, 208, 252, 0.4);
        }

        .button:hover {
            opacity: 0.9;
        }

        .link-fallback {
            margin-top: 25px;
            padding: 15px;
            background-color: #f8f8f8;
            border-radius: 8px;
            font-size: 12px;
            word-break: break-all;
            color: #666;
        }

        .link-fallback a {
            color: #8DD0FC;
        }

        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin: 25px 0;
            font-size: 13px;
            color: #856404;
        }

        .warning strong {
            display: block;
            margin-bottom: 5px;
        }

        .expire-info {
            text-align: center;
            color: #888;
            font-size: 13px;
            margin: 20px 0;
        }

        .expire-info span {
            color: #F4AFE9;
            font-weight: 600;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .footer p {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <span>DocDot</span>
        </div>

        <div class="title">Reset Password</div>
        <div class="subtitle">Kami menerima permintaan untuk mereset password akun Anda</div>

        <div class="message">
            <p>Halo <strong>{{ $name }}</strong>,</p>
            <p>Anda menerima email ini karena kami menerima permintaan reset password untuk akun DocDot Anda. Klik
                tombol di bawah ini untuk melanjutkan:</p>
        </div>

        <div class="button-container">
            <a href="{{ $resetUrl }}" class="button">Reset Password Sekarang</a>
        </div>

        <div class="expire-info">
            Link ini akan kadaluarsa dalam <span>60 menit</span>
        </div>

        <div class="link-fallback">
            <strong>Tombol tidak berfungsi?</strong> Salin dan tempel link berikut ke browser Anda:<br>
            <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
        </div>

        <div class="warning">
            <strong>⚠️ Tidak meminta reset password?</strong>
            Jika Anda tidak meminta reset password, abaikan email ini. Password Anda tidak akan berubah kecuali Anda
            mengakses link di atas dan membuat password baru.
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p>Jika Anda memiliki pertanyaan, hubungi tim support kami.</p>
            <p>&copy; {{ date('Y') }} DocDot. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
