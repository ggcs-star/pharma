<!-- resources/views/layouts/partials/supplier-sidebar.blade.php -->
<style>
    /* Sidebar Styles - Improved (Matching first design) */
    .sidebar {
        width: 260px;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
        color: #e2e8f0;
        overflow-y: auto;
        overflow-x: hidden;
        transition: all 0.3s ease;
        z-index: 1030;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        scrollbar-width: thin;
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

    /* Brand Section */
    .sidebar h4 {
        font-size: 1.2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #38bdf8, #a78bfa);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        margin: 0;
        padding: 18px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        letter-spacing: 0.5px;
    }

    /* Menu Titles */
    .menu-title {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 700;
        color: #64748b;
        padding: 14px 20px 6px 20px;
        margin-top: 5px;
    }

    /* Menu Items */
    .sidebar a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 20px;
        margin: 2px 8px;
        color: #cbd5e1;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 500;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .sidebar a i {
        width: 20px;
        font-size: 0.9rem;
        text-align: center;
        color: #64748b;
        transition: all 0.2s ease;
    }

    .sidebar a:hover {
        background: rgba(255,255,255,0.06);
        color: #ffffff;
    }

    .sidebar a:hover i {
        color: #38bdf8;
    }

    .sidebar a.active {
        background: rgba(14, 165, 233, 0.12);
        color: #38bdf8;
    }

    .sidebar a.active i {
        color: #38bdf8;
    }

    /* Mobile Toggle Button */
    .sidebar-toggle {
        position: fixed;
        top: 15px;
        left: 15px;
        z-index: 1040;
        background: #0ea5e9;
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        cursor: pointer;
        transition: all 0.2s;
        display: none;
    }

    .sidebar-toggle:hover {
        background: #0284c7;
        transform: scale(1.02);
    }

    /* Main content adjustment */
    .main-content {
        margin-left: 260px;
        transition: all 0.3s ease;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            width: 260px;
        }
        
        .sidebar.show {
            transform: translateX(0);
        }
        
        .sidebar-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .main-content {
            margin-left: 0 !important;
        }
    }
</style>

<!-- Mobile Toggle Button -->
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="fa fa-bars"></i>
</button>

<div class="sidebar" id="sidebar">
    <h4 class="text-center py-3 border-bottom d-flex align-items-center justify-content-center">
        <img src="{{ asset('storage/images/pharma.png') }}" 
             alt="Pharma ERP Logo" 
             style="height:35px; margin-right:10px;">
        {{-- Supplier Panel --}}
    </h4>
    {{-- https://pharma.jagods.in/storage/images/pharma.png --}}
    <!-- Dashboard -->
    <div class="menu-title">
        <i class="fa fa-chart-line me-1"></i> Dashboard
    </div>
    <a href="{{ route('supplier.dashboard') }}" class="{{ request()->routeIs('supplier.dashboard') ? 'active' : '' }}">
        <i class="fa fa-home me-2"></i> Dashboard
    </a>

    <!-- Catalogs -->
    <div class="menu-title">
        <i class="fa fa-database me-1"></i> Catalogs
    </div>
    <a href="{{ route('supplier.catalogs.index') }}" class="{{ request()->routeIs('supplier.catalogs.*') ? 'active' : '' }}">
        <i class="fa fa-book me-2"></i> Catalogs
    </a>

    <!-- Stocks -->
    <div class="menu-title">
        <i class="fa fa-boxes me-1"></i> Inventory
    </div>
    <a href="{{ route('supplier.stocks.index') }}" class="{{ request()->routeIs('supplier.stocks.*') ? 'active' : '' }}">
        <i class="fa fa-chart-line me-2"></i> Stocks
    </a>

    <!-- Orders -->
    <div class="menu-title">
        <i class="fa fa-shopping-cart me-1"></i> Orders
    </div>
    <a href="{{ route('supplier.orders.index') }}" class="{{ request()->routeIs('supplier.orders.*') ? 'active' : '' }}">
        <i class="fa fa-list me-2"></i> Orders
    </a>

    <!-- Ledger -->
    <div class="menu-title">
        <i class="fa fa-book me-1"></i> Accounts
    </div>
    <a href="#" class="{{ request()->routeIs('supplier.ledger') ? 'active' : '' }}">
        <i class="fa fa-file-invoice me-2"></i> Ledger
    </a>
</div>
supplier.orders.index
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
<script>
    // Mobile sidebar toggle functionality (same as first design)
    document.addEventListener('DOMContentDOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('show');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                        sidebar.classList.remove('show');
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
        
        // Active link highlight based on current URL
        const currentUrl = window.location.pathname;
        document.querySelectorAll('.sidebar a').forEach(function(link) {
            const href = link.getAttribute('href');
            if (href && href !== '#') {
                if (currentUrl === href || (currentUrl.includes(href) && href !== '/' && currentUrl !== '/')) {
                    link.classList.add('active');
                } else if (currentUrl === '/' && href === '/supplier/dashboard') {
                    link.classList.add('active');
                }
            }
        });
    });
</script>