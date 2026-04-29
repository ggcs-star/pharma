<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Supplier Panel')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons (matching first design) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Additional styles for navbar and layout */
        body {
            background: #f1f5f9;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        
        /* Top Navbar Styles - Modern Dark */
        .top-navbar {
            background: #f0f2f6;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        
        .navbar-brand-custom {
            font-size: 1.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #38bdf8, #a78bfa);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: 0.5px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-name {
            color: #000205;
            font-size: 0.9rem;
            font-weight: 500;
            background: rgba(255,255,255,0.08);
            padding: 6px 14px;
            border-radius: 30px;
        }
        
        .logout-btn {
            background: rgba(239, 68, 68, 0.15);
            border: none;
            color: #f87171;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .logout-btn:hover {
            background: #ef4444;
            color: white;
        }
        
        /* Main content area */
        .main-content {
            margin-left: 260px;
            transition: all 0.3s ease;
            padding: 20px;
            min-height: calc(100vh - 60px);
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
        }
        
        /* Card styles matching modern design */
        .content-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 20px;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>

<body>

<!-- Modern Navbar matching sidebar design -->
<nav class="top-navbar d-flex justify-content-between align-items-center">
    <span class="navbar-brand-custom">
        <i class="fa fa-capsules me-2"></i>Supplier Portal
    </span>

    <div class="user-info">
        <span class="user-name">
            <i class="fa fa-user-circle me-1"></i>
            {{ auth('supplier')->user()->name ?? 'Supplier' }}
        </span>
<form method="POST" action="{{ route('logout') }}" class="m-0">
    @csrf
    <button
        type="submit"
        class="logout-btn"
        onclick="sessionStorage.removeItem('stockAlertShown')">

        <i class="fa fa-sign-out-alt me-1"></i>
        Logout
    </button>
</form>
    </div>
</nav>

<!-- Main content with sidebar -->
<div class="main-content" id="mainContent">
    @yield('content')
</div>

<!-- Include the sidebar partial -->
@include('supplier.layouts.partials.supplier-sidebar')
<script>
    // Adjust main content margin when sidebar toggles on mobile
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        if (sidebar && mainContent) {
            // Listen for sidebar toggle changes
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        if (window.innerWidth <= 768) {
                            if (sidebar.classList.contains('show')) {
                                mainContent.style.opacity = '0.5';
                                mainContent.style.pointerEvents = 'none';
                            } else {
                                mainContent.style.opacity = '1';
                                mainContent.style.pointerEvents = 'auto';
                            }
                        }
                    }
                });
            });
            
            observer.observe(sidebar, { attributes: true });
            
            // Reset on window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    mainContent.style.opacity = '1';
                    mainContent.style.pointerEvents = 'auto';
                }
            });
        }
    });
</script>

</body>
</html>