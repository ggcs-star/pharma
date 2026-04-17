<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pharma ERP - @yield('title', 'Dashboard')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-size: 13px;
            background: #f1f5f9;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: #1e293b;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #fff;
            overflow-y: auto;
            box-shadow: 4px 0 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            z-index: 1030;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #1e293b;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 10px;
        }

        .sidebar-brand {
            padding: 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 10px;
        }

        .sidebar-brand h4 {
            font-weight: 700;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, #38bdf8, #a78bfa);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin: 0;
            font-size: 1.2rem;
        }

        .sidebar-brand small {
            font-size: 0.65rem;
            color: #64748b;
            margin-top: 5px;
            display: block;
        }

        .sidebar a {
            color: #cbd5e1;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 18px;
            text-decoration: none;
            font-size: 13px;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
            margin: 2px 0;
        }

        .sidebar a i {
            width: 20px;
            font-size: 0.95rem;
        }

        .sidebar a:hover {
            background: rgba(255,255,255,0.06);
            color: #fff;
            border-left-color: #38bdf8;
        }

        .sidebar .active {
            background: rgba(14, 165, 233, 0.12);
            color: #38bdf8;
            border-left-color: #0ea5e9;
        }

        .menu-title {
            font-size: 0.65rem;
            text-transform: uppercase;
            padding: 12px 18px 6px;
            color: #64748b;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* ================= MAIN ================= */
        .main {
            margin-left: 260px;
            padding: 20px;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        /* ================= TOPBAR ================= */
        .topbar {
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            margin-bottom: 20px;
            border: 1px solid rgba(0,0,0,0.03);
        }

        .topbar h5 {
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar h5 i {
            color: #0ea5e9;
            font-size: 1.1rem;
        }

        .topbar .user-box {
            background: #f1f5f9;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 500;
            color: #1e293b;
            transition: all 0.2s;
        }

        .topbar .user-box:hover {
            background: #e2e8f0;
        }

        .topbar .user-box i {
            color: #0ea5e9;
        }

        /* ================= CARD ================= */
        .card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
            background: #ffffff;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        /* ================= BUTTON ================= */
        .btn {
            border-radius: 10px;
            padding: 0.45rem 1rem;
            font-weight: 500;
            font-size: 0.75rem;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0284c7, #0369a1);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(14,165,233,0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
        }

        .btn-sm {
            padding: 0.3rem 0.7rem;
            font-size: 0.7rem;
        }

        /* ================= ALERT ================= */
        .alert {
            border-radius: 12px;
            font-size: 13px;
            border: none;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .alert-warning {
            background: #fffbeb;
            color: #92400e;
            border-left: 4px solid #f59e0b;
        }

        .alert-info {
            background: #eff6ff;
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }

        /* ================= FORM ================= */
        .form-label {
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 0.4rem;
        }

        .form-control, .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.55rem 0.9rem;
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14,165,233,0.1);
        }

        /* ================= TABLE ================= */
        .table {
            margin-bottom: 0;
        }

       .table > :not(caption) > * > * {
    padding: 0.5rem;
    vertical-align: middle;
    font-size: 0.8rem;
}

        .table thead th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        /* ================= BADGE ================= */
        .badge {
            padding: 0.3rem 0.7rem;
            font-weight: 500;
            border-radius: 30px;
            font-size: 0.7rem;
        }

        /* ================= MODAL ================= */
        .modal-content {
            border: none;
            border-radius: 16px;
        }

        .modal-header {
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
        }

        .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
        }

        /* ================= PAGINATION ================= */
        .pagination {
            gap: 4px;
        }

        .page-link {
            border: 1px solid #e2e8f0;
            border-radius: 8px !important;
            padding: 0.4rem 0.75rem;
            color: #64748b;
            font-size: 0.7rem;
        }

        .page-link:hover {
            background: #f1f5f9;
            color: #0ea5e9;
        }

        .page-item.active .page-link {
            background: #0ea5e9;
            border-color: #0ea5e9;
        }

        /* ================= DROPDOWN ================= */
        .dropdown-menu {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            padding: 0.5rem;
            font-size: 0.75rem;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 0.45rem 0.9rem;
            font-size: 0.75rem;
        }

        .dropdown-item:hover {
            background: #f1f5f9;
        }

        /* ================= SCROLL ================= */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #1e293b;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 10px;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 260px;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main {
                margin-left: 0;
                padding: 15px;
            }
            
            .topbar h5 {
                font-size: 0.9rem;
            }
            
            .mobile-menu-btn {
                display: block;
            }
        }
        
        @media (min-width: 769px) {
            .mobile-menu-btn {
                display: none;
            }
        }
        
        .mobile-menu-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #0ea5e9;
            margin-right: 10px;
        }

        /* ================= UTILITIES ================= */
        .cursor-pointer {
            cursor: pointer;
        }
        
        .shadow-sm {
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        .rounded-3 {
            border-radius: 12px !important;
        }
        /* ===== GLOBAL TABLE INPUT FIX (ERP FIX) ===== */
.table td input,
.table td select {
    width: 100% !important;
    min-width: 70px;
    height: 34px;
    padding: 4px 6px;
    font-size: 13px;
    box-sizing: border-box;
}

/* Qty specific fix */
.table .qty {
    min-width: 80px !important;
    font-weight: 600;
    text-align: center;
}

/* Prevent shrink */
.table-responsive {
    overflow-x: auto;
}

.table {
    table-layout: auto;
}

/* Fix small inputs (bootstrap override) */
.form-control-sm {
    min-height: 34px !important;
    padding: 4px 6px !important;
    font-size: 13px !important;
}
/* ===== FIX SVG ICON BUG ===== */
svg {
    width: 20px !important;
    height: 20px !important;
    display: inline-block;
}

/* Only pagination (safe fix) */
.pagination svg {
    width: 16px !important;
    height: 16px !important;
}
    </style>

    @stack('styles')
</head>

<body>

{{-- Mobile Menu Toggle Button (visible on mobile) --}}
<button class="mobile-menu-btn" id="mobileMenuToggle" style="position: fixed; top: 15px; left: 15px; z-index: 1040; background: white; border-radius: 8px; padding: 8px 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <i class="fas fa-bars"></i>
</button>

{{-- Sidebar --}}
@include('layouts.partials.sidebar')

<div class="main" id="mainContent">
    {{-- Topbar --}}
    <div class="topbar d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-{{ request()->routeIs('dashboard') ? 'tachometer-alt' : (request()->routeIs('purchase.*') ? 'shopping-cart' : (request()->routeIs('sales.*') ? 'cash-register' : 'file')) }}"></i>
            @yield('title', 'Dashboard')
        </h5>
        <div class="user-box d-flex align-items-center gap-2">
            <i class="fa fa-user-circle"></i>
            {{ auth()->user()->name ?? 'Admin' }}
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fa fa-info-circle me-2"></i>
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Content --}}
    @yield('content')

</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    (function() {
        'use strict';
        
        // Mobile menu toggle
        const toggleBtn = document.getElementById('mobileMenuToggle');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.getElementById('mainContent');
        
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('show');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    if (sidebar && toggleBtn) {
                        if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                            sidebar.classList.remove('show');
                        }
                    }
                }
            });
            
            // Close sidebar on window resize if screen becomes larger
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                }
            });
        }
        
        // Auto-dismiss alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = bootstrap.Alert.getInstance(alert);
                if (bsAlert) {
                    bsAlert.close();
                }
            }, 5000);
        });
        
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Active link highlighting
        const currentPath = window.location.pathname;
        document.querySelectorAll('.sidebar a').forEach(function(link) {
            const href = link.getAttribute('href');
            if (href && href !== '#' && href !== '/') {
                if (currentPath === href || (currentPath.includes(href) && href !== '/dashboard' && currentPath !== '/')) {
                    link.classList.add('active');
                } else if (currentPath === '/' && href === '/dashboard') {
                    link.classList.add('active');
                }
            }
        });
    })();
</script>

@stack('scripts')

</body>
</html>