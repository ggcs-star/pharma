<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | RAPID RETAIL</title>
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
        
        .login-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: white;
        }
        
        .login-container {
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
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .login-header h2 {
            font-size: 28px;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .login-header p {
            color: #64748b;
            font-size: 15px;
        }
        
        .form-group {
            margin-bottom: 24px;
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
        
        .password-wrapper {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .eye-icon {
            fill: #64748b;
            transition: fill 0.3s;
            width: 20px;
            height: 20px;
        }
        
        .password-toggle:hover .eye-icon {
            fill: #2563eb;
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
        
        .forgot-password {
            text-align: center;
            margin-top: 24px;
        }
        
        .forgot-password a {
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .forgot-password a:hover {
            text-decoration: underline;
        }
        
        .register-section {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
        }
        
        .register-text {
            color: #64748b;
            font-size: 15px;
            margin-bottom: 15px;
        }
        
        .register-btn {
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
        
        .register-btn:hover {
            background: #2563eb;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.2);
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-section">
            <h1>Join ShopNow Today</h1>
            <p>Create your account and unlock exclusive benefits, personalized recommendations, and faster checkout experience.</p>
            
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">🎁</div>
                    <div>
                        <h4>Welcome Bonus</h4>
                        <p style="font-size: 14px; opacity: 0.8;">Get 20% off on first order</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">⭐</div>
                    <div>
                        <h4>Exclusive Deals</h4>
                        <p style="font-size: 14px; opacity: 0.8;">Member-only discounts</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">📱</div>
                    <div>
                        <h4>Wishlist</h4>
                        <p style="font-size: 14px; opacity: 0.8;">Save items for later</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">⚡</div>
                    <div>
                        <h4>Fast Checkout</h4>
                        <p style="font-size: 14px; opacity: 0.8;">Save shipping details</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="login-section">
            <div class="login-container">
                <div class="logo">RAPID <span>RETAIL</span></div>
                
                <div class="login-header">
                    <h2>Sign In</h2>
                    <p>Access your account to continue shopping</p>
                </div>
                
                <form method="POST" action="/login">
                    @csrf
                  
@if(session('success'))
    <div style="
        background:#ecfdf5;
        border:1px solid #a7f3d0;
        color:#065f46;
        padding:14px;
        border-radius:8px;
        margin-bottom:20px;
        font-size:14px;
    ">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="
        background:#fef2f2;
        border:1px solid #fee2e2;
        color:#dc2626;
        padding:14px;
        border-radius:8px;
        margin-bottom:20px;
        font-size:14px;
    ">
        {{ session('error') }}
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

                    
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email address" required>
                    </div>
                    
                    <div class="form-group">
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" placeholder="Password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <svg class="eye-icon" viewBox="0 0 24 24" width="20" height="20">
                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">Sign In to Account</button>
                </form>
                
                <div class="forgot-password">
                    <a href="/forgot-password">Forgot your password?</a>
                </div>
                
                <div class="register-section">
                    <div class="register-text">Don't have an account?</div>
                    <a href="/register" class="register-btn">Create New Account</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.querySelector('.eye-icon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = `<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" style="opacity:0.5"/><path d="M2 2l20 20M9.88 9.88a3 3 0 1 0 4.24 4.24M17.66 17.66A10.71 10.71 0 0 1 12 20.5c-5 0-9.27-3.11-11-7.5a10.84 10.84 0 0 1 3.16-4.84"/>`;
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = `<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>`;
        }
    }
    </script>
</body>
</html>