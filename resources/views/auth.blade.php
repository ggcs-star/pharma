<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'PharmaSphere 360') }} - Admin Login</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        'primary-dark': '#1e40af',
                        'bg-light': '#f8fafc',
                        'text-main': '#1e293b',
                        'text-muted': '#64748b',
                        'border-color': '#e2e8f0',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'login-card': '0 25px 50px -12px rgba(0, 0, 0, 0.08)',
                        'primary-btn': '0 4px 12px rgba(37, 99, 235, 0.2)',
                    }
                }
            }
        }
    </script>
    <style>
        .illustration-overlay-pattern {
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.05) 0%, transparent 40%);
        }
    </style>
</head>
<body class="bg-bg-light font-sans text-text-main antialiased overflow-x-hidden">

    <div class="min-h-screen flex items-stretch">
        <!-- Left Side: Professional Illustration Area -->
        <div class="hidden lg:flex flex-1 relative bg-gradient-to-br from-slate-900 via-indigo-900 to-blue-900 overflow-hidden items-center justify-center p-10">
            <div class="absolute inset-0 illustration-overlay-pattern pointer-events-none"></div>

            <div class="relative z-20 w-full max-w-lg text-center">
                <div class="mb-8 max-w-[480px] mx-auto opacity-90">
                    <svg viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto drop-shadow-2xl">
                        <circle cx="50" cy="50" r="20" fill="white" fill-opacity="0.15" />
                        <rect x="330" y="40" width="30" height="30" rx="5" fill="white" fill-opacity="0.15" />
                        <path d="M350 200 L370 200 M360 190 L360 210" stroke="white" stroke-width="4" stroke-opacity="0.2" />
                        <path d="M120 280 C120 230 150 200 180 200 L180 280 Z" fill="white" fill-opacity="0.9" />
                        <circle cx="150" cy="175" r="30" fill="white" fill-opacity="0.9" />
                        <path d="M220 280 C220 230 250 205 280 205 L280 280 Z" fill="white" fill-opacity="0.9" />
                        <circle cx="250" cy="180" r="28" fill="white" fill-opacity="0.9" />
                        <path d="M140 210 Q150 240 160 210" fill="none" stroke="#1d4ed8" stroke-width="3" />
                    </svg>
                </div>

                <h1 class="text-[2.5rem] font-extrabold text-white tracking-tight mb-4">PharmaSphere 360</h1>
                <p class="text-lg text-white/90 font-medium max-w-md mx-auto leading-relaxed">
                    The ultimate digital ecosystem for modern pharmaceutical enterprise resource planning.
                </p>
            </div>
        </div>

        <!-- Right Side: Login Form Area -->
        <div class="flex flex-1 items-center justify-center p-8 lg:p-12 relative bg-gradient-to-br from-slate-200 to-indigo-200">
            <div class="w-full max-w-[420px]">
                <div class="text-center mb-8">
    <img 
    src="{{ asset('storage/images/pharma.png') }}" 
    class="mx-auto w-60 h-auto mb-4"

    >
</div>
<!--                 
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white shadow-sm">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                        </div>
                        <span class="text-2xl font-bold text-text-main">PharmaSphere 360</span>
                    </div>
                    <p class="text-text-muted text-[0.95rem] font-medium uppercase tracking-wider">Admin Login Panel</p>
                </div> -->

<div class="bg-gradient-to-br from-slate-50 to-blue-50 rounded-[20px] p-10 shadow-login-card border border-blue-100">                    <!-- Updated with Laravel standard form handlers -->
                    <form action="{{ route('login') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="p-3 mb-4 text-sm text-red-600 bg-red-50 rounded-lg">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-text-main"> Email Address</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                class="block w-full px-4 py-3 bg-[#fcfdfe] border border-border-color rounded-xl text-text-main text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none"
                                placeholder="name@company.com"
                            />
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-semibold text-text-main">Secure Password</label>
                            </div>
                            <div class="relative">
                                <input
                                    id="passwordInput"
                                    name="password"
                                    type="password"
                                    required
                                    class="block w-full px-4 py-3 bg-[#fcfdfe] border border-border-color rounded-xl text-text-main text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none pr-14"
                                    placeholder="••••••••"
                                />
                                <button
                                    type="button"
                                    id="togglePassword"
                                    class="absolute inset-y-0 right-3 flex items-center text-text-muted hover:text-text-main text-xs font-bold uppercase tracking-widest px-1"
                                >
                                    Show
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm py-1">
                            <label class="flex items-center gap-2 text-text-muted cursor-pointer font-medium">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-primary border-border-color rounded-sm">
                                <span>Stay signed in</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="font-semibold text-primary">Forgot access?</a>
                            @endif
                        </div>

                        <button
                            type="submit"
                            class="w-full py-3.5 bg-gradient-to-r from-primary to-primary-dark text-white rounded-xl font-semibold shadow-primary-btn hover:opacity-95 transition-all"
                        >
                            Enter Dashboard
                        </button>
                    </form>
                </div>

                <footer class="mt-20 lg:absolute lg:bottom-6 lg:left-0 lg:right-0 text-center text-text-muted text-[0.8rem] font-medium">
                    &copy; 2026 PharmaSphere 360 &bull; Enterprise Solutions
                </footer>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const pwd = document.getElementById('passwordInput');
            const type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
            pwd.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Show' : 'Hide';
        });
    </script>
</body>
</html>