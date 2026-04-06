<!-- Improved Topbar Design -->
<style>
    .topbar {
        background: #ffffff;
        padding: 0.75rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
        z-index: 1020;
        background: rgba(255,255,255,0.98);
        backdrop-filter: blur(4px);
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    /* Page Title Section */
    .page-title-section {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title-section h5 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .page-title-section h5 i {
        color: #0ea5e9;
        font-size: 1.1rem;
    }

    .breadcrumb-custom {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.7rem;
        color: #64748b;
    }

    .breadcrumb-custom a {
        color: #64748b;
        text-decoration: none;
        transition: color 0.2s;
    }

    .breadcrumb-custom a:hover {
        color: #0ea5e9;
    }

    .breadcrumb-custom .separator {
        color: #cbd5e1;
        font-size: 0.6rem;
    }

    .breadcrumb-custom .current {
        color: #0f172a;
        font-weight: 500;
    }

    /* User Section */
    .user-section {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    /* Notification Icon */
    .notification-icon {
        position: relative;
        cursor: pointer;
    }

    .notification-icon i {
        font-size: 1.1rem;
        color: #64748b;
        transition: color 0.2s;
    }

    .notification-icon:hover i {
        color: #0ea5e9;
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -8px;
        background: #ef4444;
        color: white;
        font-size: 0.55rem;
        font-weight: 600;
        padding: 2px 5px;
        border-radius: 20px;
        min-width: 16px;
        text-align: center;
    }

    /* User Dropdown */
    .user-dropdown {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 30px;
        transition: all 0.2s ease;
        background: #f8fafc;
    }

    .user-dropdown:hover {
        background: #f1f5f9;
    }

    .user-avatar {
        width: 34px;
        height: 34px;
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.8rem;
    }

    .user-role {
        font-size: 0.65rem;
        color: #64748b;
    }

    .dropdown-arrow {
        color: #94a3b8;
        font-size: 0.7rem;
        margin-left: 5px;
    }

    /* Dropdown Menu */
    .user-dropdown-menu {
        position: absolute;
        top: 60px;
        right: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        min-width: 200px;
        display: none;
        z-index: 1050;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .user-dropdown-menu.show {
        display: block;
        animation: fadeIn 0.2s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-header {
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .dropdown-header .dropdown-user-name {
        font-weight: 600;
        color: #0f172a;
        font-size: 0.85rem;
    }

    .dropdown-header .dropdown-user-email {
        font-size: 0.7rem;
        color: #64748b;
        margin-top: 2px;
    }

    .dropdown-item-custom {
        padding: 10px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #334155;
        text-decoration: none;
        font-size: 0.8rem;
        transition: all 0.2s;
        cursor: pointer;
    }

    .dropdown-item-custom i {
        width: 18px;
        font-size: 0.85rem;
        color: #64748b;
    }

    .dropdown-item-custom:hover {
        background: #f1f5f9;
        color: #0ea5e9;
    }

    .dropdown-item-custom:hover i {
        color: #0ea5e9;
    }

    .dropdown-divider {
        height: 1px;
        background: #e2e8f0;
        margin: 6px 0;
    }

    .text-danger-custom {
        color: #ef4444;
    }

    .text-danger-custom:hover {
        background: #fef2f2;
        color: #dc2626;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .topbar {
            padding: 0.6rem 1rem;
        }
        
        .user-info {
            display: none;
        }
        
        .user-dropdown {
            padding: 5px;
        }
        
        .page-title-section h5 {
            font-size: 0.9rem;
        }
        
        .breadcrumb-custom {
            display: none;
        }
    }
</style>

<div class="topbar">
    <div class="page-title-section">
        <h5>
            <i class="fas fa-{{ request()->routeIs('dashboard') ? 'tachometer-alt' : (request()->routeIs('purchase.*') ? 'shopping-cart' : (request()->routeIs('sales.*') ? 'cash-register' : 'file')) }}"></i>
            @yield('title', 'Dashboard')
        </h5>
        <div class="breadcrumb-custom">
            <a href="{{ route('dashboard') }}">Home</a>
            <span class="separator"><i class="fas fa-chevron-right fa-xs"></i></span>
            <span class="current">@yield('title', 'Dashboard')</span>
        </div>
    </div>

    <div class="user-section">
        <!-- Notification Bell -->
        <div class="notification-icon">
            <i class="far fa-bell"></i>
            <span class="notification-badge" style="display: none;">0</span>
        </div>

        <!-- User Dropdown -->
        <div class="user-dropdown" id="userDropdownBtn">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="user-info">
                <span class="user-name">{{ auth()->user()->name ?? 'Admin' }}</span>
                <span class="user-role">Administrator</span>
            </div>
            <i class="fas fa-chevron-down dropdown-arrow"></i>
        </div>
    </div>
</div>

<!-- Dropdown Menu -->
<div class="user-dropdown-menu" id="userDropdownMenu">
    <div class="dropdown-header">
        <div class="dropdown-user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
        <div class="dropdown-user-email">{{ auth()->user()->email ?? 'admin@pharma.com' }}</div>
    </div>
    <a href="{{ route('profile.edit') }}" class="dropdown-item-custom">
        <i class="fas fa-user-circle"></i>
        <span>My Profile</span>
    </a>
    <a href="{{ route('settings.index') }}" class="dropdown-item-custom">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
    </a>
    <div class="dropdown-divider"></div>
    <form method="POST" action="{{ route('logout') }}" id="logout-form-topbar" style="display: none;">
        @csrf
    </form>
    <a href="#" class="dropdown-item-custom text-danger-custom" onclick="event.preventDefault(); document.getElementById('logout-form-topbar').submit();">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </a>
</div>

<script>
    // User Dropdown Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownBtn = document.getElementById('userDropdownBtn');
        const dropdownMenu = document.getElementById('userDropdownMenu');
        
        if (dropdownBtn && dropdownMenu) {
            dropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!dropdownBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
        }
        
        // Optional: Fetch notification count via AJAX
        // You can uncomment and implement this as needed
        /*
        fetch('/api/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.notification-badge');
                if (data.count > 0) {
                    badge.style.display = 'inline-block';
                    badge.textContent = data.count > 9 ? '9+' : data.count;
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(err => console.error('Error fetching notifications:', err));
        */
    });
</script>