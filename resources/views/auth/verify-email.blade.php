<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | RAPID RETAIL</title>
    <style>
              .logo span {
            margin-left: 8px;
    }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        
        body {
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
        }
        
        .container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        
        .image-section {
            flex: 1;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            color: white;
        }
        
        .image-section h1 {
            font-size: 42px;
            margin-bottom: 20px;
            font-weight: 700;
            line-height: 1.2;
        }
        
        .image-section p {
            font-size: 18px;
            opacity: 0.9;
            line-height: 1.6;
            max-width: 500px;
        }
        
        .features {
            margin-top: 40px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .feature-icon {
            background: rgba(255, 255, 255, 0.1);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        
        .verify-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: white;
        }
        
        .verify-container {
            width: 100%;
            max-width: 420px;
        }
        
        .logo {
            font-size: 32px;
            font-weight: 800;
            color: #2563eb;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .logo span {
            color: #7c3aed;
        }
        
        .verify-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .verify-header h2 {
            font-size: 28px;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .verify-header p {
            color: #64748b;
            font-size: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            color: #475569;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
        }
        
        .form-group input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            background: #f8fafc;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #2563eb;
            background: white;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .otp-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .otp-input {
            flex: 1;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
        }
        
        .otp-input:focus {
            border-color: #2563eb;
            background: white;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .timer {
            text-align: center;
            margin: 15px 0;
            color: #64748b;
            font-size: 14px;
        }
        
        .timer span {
            color: #2563eb;
            font-weight: 600;
        }
        
        .resend-link {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .resend-btn {
            color: #2563eb;
            background: none;
            border: none;
            font-weight: 500;
            cursor: pointer;
            font-size: 14px;
            text-decoration: underline;
        }
        
        .resend-btn:hover {
            color: #1d4ed8;
        }
        
        .resend-btn.disabled {
            color: #94a3b8;
            cursor: not-allowed;
            text-decoration: none;
        }
        
        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.2);
        }
        
        .login-section {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
        }
        
        .login-text {
            color: #64748b;
            font-size: 15px;
            margin-bottom: 15px;
        }
        
        .login-btn {
            display: inline-block;
            padding: 14px 32px;
            background: white;
            border: 2px solid #2563eb;
            border-radius: 10px;
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .login-btn:hover {
            background: #2563eb;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.2);
        }
        
        .back-section {
            margin-bottom: 30px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: #2563eb;
        }
        
        .success-message {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            color: #166534;
            font-size: 14px;
        }
        
        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
            }
            
            .image-section {
                padding: 40px;
                text-align: center;
            }
            
            .features {
                justify-content: center;
            }
        }
        
        @media (max-width: 768px) {
            .image-section h1 {
                font-size: 32px;
            }
            
            .features {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .otp-container {
                gap: 8px;
            }
            
            .otp-input {
                height: 50px;
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-section">
            <h1>Verify Your Email</h1>
            <p>Enter the 6-digit OTP sent to your email address to complete the verification process.</p>
            
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">✉️</div>
                    <div>
                        <h4>Email Sent</h4>
                        <p style="font-size: 14px; opacity: 0.8;">Check your inbox for OTP</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">⏱️</div>
                    <div>
                        <h4>Time Limited</h4>
                        <p style="font-size: 14px; opacity: 0.8;">OTP expires in 10 minutes</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✅</div>
                    <div>
                        <h4>Secure Access</h4>
                        <p style="font-size: 14px; opacity: 0.8;">One-time use only</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🔄</div>
                    <div>
                        <h4>Can't find it?</h4>
                        <p style="font-size: 14px; opacity: 0.8;">Resend OTP available</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="verify-section">
            <div class="verify-container">
                <div class="logo">INVENTORY<span>MANAGEMENT</span></div>
                
                <div class="back-section">
                    <a href="/register" class="back-link">
                        ← Back to Registration
                    </a>
                </div>
                
                <div class="verify-header">
                    <h2>Email Verification</h2>
                    <p>Enter the OTP sent to your email address</p>
                </div>
                
                <form method="POST" action="/verify-email">
                    @csrf
                    
@if(session('success'))
    <div class="success-message">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="error-container">
        <ul class="error-list">
            <li>{{ session('error') }}</li>
        </ul>
    </div>
@endif

@if ($errors->any())
    <div class="error-container">
        <ul class="error-list">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

                
                    <input type="hidden" name="email" value="{{ $email }}">
                    <div class="form-group">
                        <label class="form-label">OTP Code</label>
                        <div class="otp-container">
                            <input type="text" name="otp" maxlength="6" class="otp-input" placeholder="000000" required>
                        </div>
                    </div>
                    
                    <div class="timer">
                        Time remaining: <span id="countdown">10:00</span>
                    </div>
                    
                    <div class="resend-link">
                        <button type="button" class="resend-btn" id="resendBtn" disabled>
                            Resend OTP (60s)
                        </button>
                    </div>
                    
                    <button type="submit" class="submit-btn">Verify Email</button>
                </form>
                
                <div class="login-section">
                    <div class="login-text">Already verified your email?</div>
                    <a href="/login" class="login-btn">Sign In Now</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Timer functionality
        let totalSeconds = 600; // 10 minutes
        let resendTimer = 60; // 60 seconds for resend
        const countdownEl = document.getElementById('countdown');
        const resendBtn = document.getElementById('resendBtn');
        
        function updateTimer() {
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            countdownEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (totalSeconds > 0) {
                totalSeconds--;
                setTimeout(updateTimer, 1000);
            } else {
                countdownEl.textContent = "00:00";
                countdownEl.style.color = "#dc2626";
            }
        }
        
        function updateResendTimer() {
            if (resendTimer > 0) {
                resendBtn.textContent = `Resend OTP (${resendTimer}s)`;
                resendTimer--;
                setTimeout(updateResendTimer, 1000);
            } else {
                resendBtn.textContent = "Resend OTP";
                resendBtn.classList.remove('disabled');
                resendBtn.disabled = false;
            }
        }
        
        resendBtn.addEventListener('click', function() {
            if (!this.disabled) {
                // Trigger resend OTP functionality
                alert('OTP has been resent to your email!');
                this.classList.add('disabled');
                this.disabled = true;
                resendTimer = 60;
                updateResendTimer();
            }
        });
        
        // Auto-focus OTP input
        document.querySelector('.otp-input').focus();
        
        // Start timers
        updateTimer();
        updateResendTimer();
        
        // Auto move to next input (if using multiple OTP inputs)
        const otpInput = document.querySelector('.otp-input');
        otpInput.addEventListener('input', function(e) {
            if (this.value.length === this.maxLength) {
                document.querySelector('.submit-btn').focus();
            }
        });
    </script>
</body>
</html>