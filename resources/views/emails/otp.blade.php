<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Email - DocDot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            width: 120px;
            height: auto;
        }
        .title {
            color: #DDB4F6;
            text-align: center;
            font-size: 24px;
            margin: 20px 0;
            font-weight: bold;
        }
        .otp-code {
            background-color: #ffffff;
            padding: 20px;
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            margin: 30px 0;
            border: 2px dashed #DDB4F6;
            border-radius: 8px;
            letter-spacing: 8px;
            white-space: nowrap;
        }
        .message {
            color: #333;
            font-size: 16px;
            margin: 15px 0;
            line-height: 1.8;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <span style="font-size: 32px; font-weight: bold; color: #1b1b18;">DocDot</span>
        </div>
        
        <div class="title">Verifikasi Email DocDot</div>
        
        <div class="message">
            <p>Halo {{ $name }},</p>
            <p>Terima kasih telah mendaftar di DocDot. Untuk menyelesaikan proses verifikasi email Anda, masukkan kode OTP berikut:</p>
        </div>
        
        <div class="otp-code">{{ $otp }}</div>
        
        <p>Kode OTP ini akan kadaluarsa dalam 10 menit.</p>
        
        <p style="color: #ff4444; margin-top: 20px;">JANGAN BERIKAN kode ini kepada siapapun, termasuk pihak yang mengaku dari DocDot.</p>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} DocDot. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
