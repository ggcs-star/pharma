<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | RAPID RETAIL</title>
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
        
        .reset-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: white;
        }
        
        .reset-container {
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
        
        .reset-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .reset-header h2 {
            font-size: 28px;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .reset-header p {
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
        
        .error-container {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }
        
        .error-list {
            list-style: none;
        }
        
        .error-list li {
            color: #dc2626;
            font-size: 14px;
            padding: 4px 0;
            display: flex;
            align-items: center;
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
            <h1>Set New Password</h1>
            <p>Enter the OTP sent to your email and create a strong new password for your account.</p>
            
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">🔐</div>
                    <div>
                        <h4>Strong Security</h4>
                        <p style="font-size: 14px; opacity: 0.8;">Password encryption enabled</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">⏱️</div>
                    <div>
                        <h4>OTP Expires</h4>
                        <p style="font-size: 14px; opacity: 0.8;">Valid for 10 minutes only</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✅</div>
                    <div>
                        <h4>Instant Update</h4>
                        <p style="font-size: 14px; opacity: 0.8;">Password updates immediately</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🔍</div>
                    <div>
                        <h4>Strength Check</h4>
                        <p style="font-size: 14px; opacity: 0.8;">Real-time password strength</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="reset-section">
            <div class="reset-container">
                <div class="logo">INVENTORY<span>MANAGEMENT</span></div>
                
                <div class="back-section">
                    <a href="/forgot-password" class="back-link">
                        ← Back to Forgot Password
                    </a>
                </div>
                
                <div class="reset-header">
                    <h2>Reset Password</h2>
                    <p>Enter OTP and set your new password</p>
                </div>
                
                <form method="POST" action="/reset-password">
                    @csrf
                 
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

                    
                    <input type="hidden" name="email" value="{{ session('reset_email') }}">

                    
                    <div class="form-group">
                        <label class="form-label">OTP Code</label>
                        <div class="otp-container">
                            <input type="text" name="otp" maxlength="6" class="otp-input" placeholder="000000" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" placeholder="Enter new password" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" placeholder="Confirm new password" required>
                    </div>
                    
                    <button type="submit" class="submit-btn">Reset Password</button>
                </form>
                
                <div class="login-section">
                    <div class="login-text">Remember your password?</div>
                    <a href="/login" class="login-btn">Sign In Instead</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>