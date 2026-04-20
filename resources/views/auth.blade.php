<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'PharmaSphere 360') }} - Unified Login Portal</title>
    
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
                        'supplier-primary': '#059669',
                        'supplier-dark': '#047857',
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
                        'supplier-btn': '0 4px 12px rgba(5, 150, 105, 0.2)',
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
        .role-tab {
            transition: all 0.3s ease;
        }
        .role-tab.active {
            background: white;
            color: #1e293b;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .role-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            border-radius: 2px;
        }
        .role-tab.admin.active::after {
            background: #2563eb;
        }
        .role-tab.supplier.active::after {
            background: #059669;
        }
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-bg-light font-sans text-text-main antialiased overflow-x-hidden">

    <div class="min-h-screen flex items-stretch">
        <!-- Left Side: Professional Illustration Area -->
        <div id="brandPanel" class="hidden lg:flex flex-1 relative bg-gradient-to-br from-slate-900 via-indigo-900 to-blue-900 overflow-hidden items-center justify-center p-10 transition-all duration-500">
            <div class="absolute inset-0 illustration-overlay-pattern pointer-events-none"></div>

            <div class="relative z-20 w-full max-w-lg text-center">
                <div class="mb-8 max-w-[480px] mx-auto opacity-90">
                    <svg id="brandIcon" viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto drop-shadow-2xl">
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

                <h1 id="brandTitle" class="text-[2.5rem] font-extrabold text-white tracking-tight mb-4">PharmaSphere 360</h1>
                <p id="brandSubtitle" class="text-lg text-white/90 font-medium max-w-md mx-auto leading-relaxed">
                    The ultimate digital ecosystem for modern pharmaceutical enterprise resource planning.
                </p>
                <div id="roleBadge" class="mt-6 inline-block px-4 py-1.5 rounded-full bg-white/20 backdrop-blur-sm text-white text-sm font-semibold">
                    Admin Portal
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form Area -->
        <div class="flex flex-1 items-center justify-center p-8 lg:p-12 relative bg-gradient-to-br from-slate-200 to-indigo-200">
            <div class="w-full max-w-[420px]">
                <div class="text-center mb-8">
                    <img 
                        src="{{ asset('storage/images/pharma.png') }}" 
                        class="mx-auto w-60 h-auto mb-4"
                        onerror="this.style.display='none'"
                        alt="PharmaSphere Logo"
                    >
                </div>

                <!-- Role Switcher Tabs -->
                <div class="flex gap-2 mb-6 bg-white/60 backdrop-blur-sm rounded-2xl p-1.5 shadow-sm">
                    <button 
                        id="adminTab" 
                        class="role-tab admin flex-1 py-2.5 rounded-xl font-semibold text-sm relative transition-all duration-200 active"
                        onclick="switchRole('admin')"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Admin Access
                        </span>
                    </button>
                    <button 
                        id="supplierTab" 
                        class="role-tab supplier flex-1 py-2.5 rounded-xl font-semibold text-sm relative transition-all duration-200 text-text-muted hover:bg-white/40"
                        onclick="switchRole('supplier')"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Supplier Portal
                        </span>
                    </button>
                </div>

                <!-- Admin Login Form -->
                <div id="adminForm" class="bg-gradient-to-br from-slate-50 to-blue-50 rounded-[20px] p-10 shadow-login-card border border-blue-100 fade-in">
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
                            <label class="text-sm font-semibold text-text-main">Email Address</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                class="block w-full px-4 py-3 bg-[#fcfdfe] border border-border-color rounded-xl text-text-main text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none"
                                placeholder="admin@company.com"
                            />
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-text-main">Secure Password</label>
                            <div class="relative">
                                <input
                                    id="adminPassword"
                                    name="password"
                                    type="password"
                                    required
                                    class="block w-full px-4 py-3 bg-[#fcfdfe] border border-border-color rounded-xl text-text-main text-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none pr-14"
                                    placeholder="••••••••"
                                />
                                <button
                                    type="button"
                                    onclick="togglePassword('adminPassword', this)"
                                    class="absolute inset-y-0 right-3 flex items-center text-text-muted hover:text-text-main text-xs font-bold uppercase tracking-widest px-1"
                                >
                                    Show
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center text-sm py-1">
                            <label class="flex items-center gap-2 text-text-muted cursor-pointer font-medium">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-primary border-border-color rounded-sm">
                                <span>Stay signed in</span>
                            </label>
                        </div>

                        <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-primary to-primary-dark text-white rounded-xl font-semibold shadow-primary-btn hover:opacity-95 transition-all">
                            Enter Admin Dashboard
                        </button>
                    </form>
                </div>

                <!-- Supplier Login Form (Hidden by default) -->
                <div id="supplierForm" style="display: none;" class="bg-gradient-to-br from-slate-50 to-emerald-50 rounded-[20px] p-10 shadow-login-card border border-emerald-100 fade-in">
                    <form action="{{ route('supplier.login.submit') }}" method="POST" class="space-y-5">
                        @csrf

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-text-main">Supplier Email</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                class="block w-full px-4 py-3 bg-[#fcfdfe] border border-border-color rounded-xl text-text-main text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none"
                                placeholder="supplier@company.com"
                            />
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-text-main">Password</label>
                            <div class="relative">
                                <input
                                    id="supplierPassword"
                                    name="password"
                                    type="password"
                                    required
                                    class="block w-full px-4 py-3 bg-[#fcfdfe] border border-border-color rounded-xl text-text-main text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none pr-14"
                                    placeholder="••••••••"
                                />
                                <button
                                    type="button"
                                    onclick="togglePassword('supplierPassword', this)"
                                    class="absolute inset-y-0 right-3 flex items-center text-text-muted hover:text-text-main text-xs font-bold uppercase tracking-widest px-1"
                                >
                                    Show
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center text-sm py-1">
                            <label class="flex items-center gap-2 text-text-muted cursor-pointer font-medium">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-emerald-600 border-border-color rounded-sm">
                                <span>Remember me</span>
                            </label>
                        </div>

                        <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl font-semibold shadow-supplier-btn hover:opacity-95 transition-all">
                            Access Supplier Hub
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
        // Toggle password visibility
        function togglePassword(inputId, button) {
            const pwd = document.getElementById(inputId);
            const type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
            pwd.setAttribute('type', type);
            button.textContent = type === 'password' ? 'Show' : 'Hide';
        }

        // Switch between Admin and Supplier roles
        function switchRole(role) {
            const adminForm = document.getElementById('adminForm');
            const supplierForm = document.getElementById('supplierForm');
            const adminTab = document.getElementById('adminTab');
            const supplierTab = document.getElementById('supplierTab');
            const brandPanel = document.getElementById('brandPanel');
            const brandTitle = document.getElementById('brandTitle');
            const brandSubtitle = document.getElementById('brandSubtitle');
            const roleBadge = document.getElementById('roleBadge');
            const brandIcon = document.getElementById('brandIcon');

            if (role === 'admin') {
                // Show admin form, hide supplier form
                adminForm.style.display = 'block';
                supplierForm.style.display = 'none';
                
                // Update tab styles
                adminTab.classList.add('active', 'bg-white', 'text-text-main', 'shadow-sm');
                adminTab.classList.remove('text-text-muted');
                supplierTab.classList.remove('active', 'bg-white', 'text-text-main', 'shadow-sm');
                supplierTab.classList.add('text-text-muted');
                
                // Update left panel branding
                if (brandPanel) {
                    brandPanel.className = "hidden lg:flex flex-1 relative bg-gradient-to-br from-slate-900 via-indigo-900 to-blue-900 overflow-hidden items-center justify-center p-10 transition-all duration-500";
                    brandTitle.textContent = "PharmaSphere 360";
                    brandSubtitle.textContent = "The ultimate digital ecosystem for modern pharmaceutical enterprise resource planning.";
                    roleBadge.textContent = "Admin Portal • Full Control";
                    brandIcon.innerHTML = `<circle cx="50" cy="50" r="20" fill="white" fill-opacity="0.15" />
                        <rect x="330" y="40" width="30" height="30" rx="5" fill="white" fill-opacity="0.15" />
                        <path d="M350 200 L370 200 M360 190 L360 210" stroke="white" stroke-width="4" stroke-opacity="0.2" />
                        <path d="M120 280 C120 230 150 200 180 200 L180 280 Z" fill="white" fill-opacity="0.9" />
                        <circle cx="150" cy="175" r="30" fill="white" fill-opacity="0.9" />
                        <path d="M220 280 C220 230 250 205 280 205 L280 280 Z" fill="white" fill-opacity="0.9" />
                        <circle cx="250" cy="180" r="28" fill="white" fill-opacity="0.9" />
                        <path d="M140 210 Q150 240 160 210" fill="none" stroke="#1d4ed8" stroke-width="3" />`;
                }
            } else {
                // Show supplier form, hide admin form
                adminForm.style.display = 'none';
                supplierForm.style.display = 'block';
                
                // Update tab styles
                supplierTab.classList.add('active', 'bg-white', 'text-text-main', 'shadow-sm');
                supplierTab.classList.remove('text-text-muted');
                adminTab.classList.remove('active', 'bg-white', 'text-text-main', 'shadow-sm');
                adminTab.classList.add('text-text-muted');
                
                // Update left panel branding
                if (brandPanel) {
                    brandPanel.className = "hidden lg:flex flex-1 relative bg-gradient-to-br from-emerald-900 via-teal-800 to-green-900 overflow-hidden items-center justify-center p-10 transition-all duration-500";
                    brandTitle.textContent = "Supplier Connect";
                    brandSubtitle.textContent = "Seamless inventory management, order processing & real-time collaboration with PharmaSphere network.";
                    roleBadge.textContent = "Supplier Portal • Partner Access";
                    brandIcon.innerHTML = `<circle cx="60" cy="60" r="24" fill="white" fill-opacity="0.2" />
                        <path d="M280 250 L320 250 M300 230 L300 270" stroke="white" stroke-width="5" stroke-opacity="0.3" />
                        <rect x="100" y="230" width="80" height="60" rx="8" fill="white" fill-opacity="0.85" />
                        <rect x="120" y="210" width="40" height="25" rx="6" fill="white" fill-opacity="0.9" />
                        <path d="M240 280 C240 220 280 195 310 195 L310 280 Z" fill="white" fill-opacity="0.9" />
                        <circle cx="275" cy="185" r="22" fill="white" fill-opacity="0.95" />
                        <path d="M170 220 L185 245 L200 220" fill="none" stroke="#047857" stroke-width="3" />`;
                }
            }
        }

        // Store the selected role in localStorage to maintain state
        document.addEventListener('DOMContentLoaded', function() {
            const savedRole = localStorage.getItem('selectedRole');
            if (savedRole === 'supplier') {
                switchRole('supplier');
            } else {
                switchRole('admin');
            }
            
            // Save role before form submission
            const adminForm = document.getElementById('adminForm').querySelector('form');
            const supplierFormElem = document.getElementById('supplierForm').querySelector('form');
            
            if (adminForm) {
                adminForm.addEventListener('submit', function() {
                    localStorage.setItem('selectedRole', 'admin');
                });
            }
            
            if (supplierFormElem) {
                supplierFormElem.addEventListener('submit', function() {
                    localStorage.setItem('selectedRole', 'supplier');
                });
            }
        });
    </script>
</body>
</html>