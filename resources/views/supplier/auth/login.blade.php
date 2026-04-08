<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Login | Pharma ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background Elements */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.15;
            z-index: 0;
            animation: float 20s infinite ease-in-out;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            background: #38bdf8;
            top: -100px;
            left: -100px;
        }

        .shape-2 {
            width: 400px;
            height: 400px;
            background: #a78bfa;
            bottom: -150px;
            right: -150px;
            animation-delay: -5s;
        }

        .shape-3 {
            width: 200px;
            height: 200px;
            background: #10b981;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            33% {
                transform: translate(30px, -30px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        /* Login Card */
        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            margin: 20px;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 32px;
            padding: 40px 36px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.3);
        }

        .logo-icon i {
            font-size: 2rem;
            color: white;
        }

        .logo-section h2 {
            font-size: 1.6rem;
            font-weight: 800;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 8px;
        }

        .logo-section p {
            color: #64748b;
            font-size: 0.85rem;
            margin: 0;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #475569;
            display: block;
            margin-bottom: 8px;
        }

        .form-label i {
            margin-right: 8px;
            color: #0ea5e9;
            font-size: 0.8rem;
        }

        .input-group-custom {
            position: relative;
        }

        .input-group-custom i.input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
            z-index: 1;
        }

        .form-control-custom {
            width: 100%;
            padding: 14px 16px 14px 44px;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            background: #fafcff;
            color: #0f172a;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
            background: white;
        }

        .form-control-custom::placeholder {
            color: #cbd5e1;
            font-size: 0.85rem;
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.2s;
            z-index: 1;
        }

        .password-toggle:hover {
            color: #0ea5e9;
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            border: none;
            padding: 14px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 0.9rem;
            color: white;
            transition: all 0.2s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #0284c7, #2563eb);
            box-shadow: 0 10px 20px -5px rgba(14, 165, 233, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Alert Styles */
        .alert-custom {
            border-radius: 16px;
            padding: 14px 18px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 4px solid;
            animation: shake 0.3s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .alert-danger-custom {
            background: #fef2f2;
            border-left-color: #ef4444;
            color: #b91c1c;
        }

        .alert-danger-custom i {
            color: #ef4444;
            font-size: 1.1rem;
        }

        /* Footer Links */
        .login-footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #eef2ff;
        }

        .login-footer a {
            color: #64748b;
            font-size: 0.75rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .login-footer a:hover {
            color: #0ea5e9;
        }

        /* Remember Me Checkbox */
        .checkbox-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.8rem;
            color: #475569;
        }

        .checkbox-label input {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #0ea5e9;
        }

        .forgot-link {
            font-size: 0.75rem;
            color: #0ea5e9;
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 24px;
            }

            .logo-icon {
                width: 55px;
                height: 55px;
            }

            .logo-icon i {
                font-size: 1.5rem;
            }

            .logo-section h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>

<div class="bg-shape shape-1"></div>
<div class="bg-shape shape-2"></div>
<div class="bg-shape shape-3"></div>

<div class="login-container">
    <div class="login-card">
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fa fa-capsules"></i>
            </div>
            <h2>Supplier Portal</h2>
            <p>Sign in to access your dashboard</p>
        </div>

        @if(session('error'))
            <div class="alert-custom alert-danger-custom">
                <i class="fa fa-exclamation-triangle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert-custom alert-danger-custom">
                <i class="fa fa-exclamation-circle"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('supplier.login.submit') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">
                    <i class="fa fa-envelope"></i> Email Address
                </label>
                <div class="input-group-custom">
                    <i class="fa fa-envelope input-icon"></i>
                    <input type="email" 
                           name="email" 
                           class="form-control-custom" 
                           placeholder="supplier@example.com"
                           value="{{ old('email') }}"
                           required 
                           autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fa fa-lock"></i> Password
                </label>
                <div class="input-group-custom">
                    <i class="fa fa-lock input-icon"></i>
                    <input type="password" 
                           name="password" 
                           id="password"
                           class="form-control-custom" 
                           placeholder="Enter your password"
                           required>
                    <i class="fa fa-eye-slash password-toggle" id="togglePassword"></i>
                </div>
            </div>

            <div class="checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>
                <a href="#" class="forgot-link">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-login">
                <i class="fa fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <div class="login-footer">
            <a href="{{ url('/') }}">
                <i class="fa fa-arrow-left"></i> Back to Home
            </a>
            <span style="color: #e2e8f0; margin: 0 12px;">|</span>
            <a href="#">
                <i class="fa fa-headset"></i> Need Help?
            </a>
        </div>
    </div>
</div>

<script>
    // Password visibility toggle
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    // Add loading state on form submit
    const loginForm = document.querySelector('form');
    const loginBtn = document.querySelector('.btn-login');

    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function() {
            loginBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Signing in...';
            loginBtn.disabled = true;
        });
    }

    // Remove error alert on input focus
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            const alert = document.querySelector('.alert-custom');
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }
        });
    });
</script>

</body>
</html>