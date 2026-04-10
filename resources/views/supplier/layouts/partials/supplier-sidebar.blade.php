{{-- resources/views/layouts/partials/supplier-sidebar.blade.php --}}
<style>
    /* Sidebar Styles - Clean UI matching the reference design */
    .sidebar {
        width: 260px;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        background: #0f172a;
        color: #e2e8f0;
        overflow-y: auto;
        overflow-x: hidden;
        transition: all 0.3s ease;
        z-index: 1030;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
        border-right: 1px solid #1e293b;
        scrollbar-width: thin;
    }

    .sidebar::-webkit-scrollbar {
        width: 5px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: #0f172a;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 10px;
    }

    /* Brand Section */
    .sidebar h4 {
        font-weight: 600;
        letter-spacing: -0.01em;
        color: white;
        background: rgba(255, 255, 255, 0.02);
        margin: 0;
        padding: 1.2rem 0;
        border-bottom: 1px solid #334155 !important;
    }

    .sidebar h4 img {
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    }

    /* Menu Titles */
    .menu-title {
        text-transform: uppercase;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: #94a3b8;
        padding: 1.2rem 1.2rem 0.4rem 1.5rem;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .menu-title i {
        font-size: 0.8rem;
        width: 16px;
        opacity: 0.7;
    }

    /* Menu Items */
    .sidebar a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.6rem 1.2rem 0.6rem 1.5rem;
        margin: 2px 8px;
        color: #cbd5e1;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 450;
        border-radius: 10px;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .sidebar a i {
        width: 20px;
        font-size: 0.9rem;
        text-align: center;
        opacity: 0.85;
        flex-shrink: 0;
    }

    .sidebar a:hover {
        background: #1e293b;
        color: #f1f5f9;
    }

    .sidebar a.active {
        background: #2563eb;
        color: white;
        box-shadow: 0 6px 12px -6px rgba(37, 99, 235, 0.3);
    }

    .sidebar a.active i {
        color: white;
        opacity: 1;
    }

    /* Logout Button */
    .sidebar form button {
        width: 100%;
        background: none;
        border: none;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 20px 10px 1.5rem;
        margin: 2px 8px;
        color: #cbd5e1;
        font-size: 0.9rem;
        font-weight: 450;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.15s;
        text-align: left;
    }

    .sidebar form button:hover {
        background: #1e293b;
        color: #f8fafc;
    }

    .sidebar form button i {
        width: 20px;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    /* Mobile Toggle Button */
    .sidebar-toggle {
        position: fixed;
        top: 15px;
        left: 15px;
        z-index: 1040;
        background: #2563eb;
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        cursor: pointer;
        transition: all 0.2s;
        display: none;
    }

    .sidebar-toggle:hover {
        background: #1d4ed8;
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
        <img src="{{ asset('storage/images/pharma.png') }}" alt="Pharma ERP Logo"
            style="height:35px; margin-right:10px;">
    </h4>

    <!-- Dashboard -->
    <div class="menu-title">
        <i class="fa fa-chart-line me-1"></i> Dashboard
    </div>
    <a href="{{ route('supplier.dashboard') }}" class="{{ request()->routeIs('supplier.dashboard') ? 'active' : '' }}">
        <i class="fa fa-home me-2"></i> Dashboard
    </a>

    <!-- Items -->
    <div class="menu-title">
        <i class="fa fa-database me-1"></i> Items
    </div>
    <a href="{{ route('supplier.items.index') }}" class="{{ request()->routeIs('supplier.items.*') ? 'active' : '' }}">
        <i class="fa fa-book me-2"></i> Items
    </a>

    <!-- Catalogs -->
    <div class="menu-title">
        <i class="fa fa-database me-1"></i> Catalogs
    </div>
    <a href="{{ route('supplier.catalogs.index') }}"
        class="{{ request()->routeIs('supplier.catalogs.*') ? 'active' : '' }}">
        <i class="fa fa-book me-2"></i> Catalogs
    </a>

    <!-- Inventory -->
    <div class="menu-title">
        <i class="fa fa-boxes me-1"></i> Inventory
    </div>
    <a href="{{ route('supplier.stocks.index') }}"
        class="{{ request()->routeIs('supplier.stocks.*') ? 'active' : '' }}">
        <i class="fa fa-chart-line me-2"></i> Stocks
    </a>

    <!-- Orders -->
    <div class="menu-title">
        <i class="fa fa-shopping-cart me-1"></i> Orders
    </div>
    <a href="{{ route('supplier.orders.index') }}"
        class="{{ request()->routeIs('supplier.orders.*') ? 'active' : '' }}">
        <i class="fa fa-list me-2"></i> Orders
    </a>

    <!-- Accounts -->
    <div class="menu-title">
        <i class="fa fa-book me-1"></i> Accounts
    </div>
    <a href="#" class="{{ request()->routeIs('supplier.ledger') ? 'active' : '' }}">
        <i class="fa fa-file-invoice me-2"></i> Ledger
    </a>

    <!-- Logout -->
    <div class="menu-title">
        <i class="fa fa-sign-out-alt me-1"></i> Session
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">
            <i class="fa fa-sign-out-alt me-2"></i> Logout
        </button>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                sidebar.classList.toggle('show');
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function (event) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });

            // Close sidebar on window resize if screen becomes larger
            window.addEventListener('resize', function () {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                }
            });
        }
    });
</script>