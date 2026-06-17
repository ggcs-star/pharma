@extends('layouts.master')

@section('content')

<div class="pharma-dashboard-wrapper">
<!-- 
    {{-- HEADER SECTION --}}
    <div class="dashboard-header">
        <div class="header-left">
            <div class="brand-logo">
                <i class="fas fa-hospital-user"></i>
                <span>PharmaSphere <span class="brand-360">360</span></span>
            </div>
            <div class="brand-tagline">
                <i class="fas fa-chart-line"></i> Intelligent Pharmacy Ecosystem
            </div>
        </div>
        <div class="header-right">
            <form method="GET" class="date-filter-form">
                <div class="filter-group">
                    <i class="fas fa-calendar-alt"></i>
                    <select name="range" class="filter-select" onchange="this.form.submit()">
                        <option value="7" {{ request('range') == 7 ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30" {{ request('range') == 30 ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="90" {{ request('range') == 90 ? 'selected' : '' }}>Last 90 Days</option>
                        <option value="month" {{ request('range') == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="today" {{ request('range') == 'today' ? 'selected' : '' }}>Today</option>
                    </select>
                </div>
            </form>
            <div class="date-time">
                <i class="fas fa-calendar-day"></i>
                <span>{{ now()->format('l, d M Y') }}</span>
                <span class="time-sep">|</span>
                <i class="fas fa-clock"></i>
                <span>{{ now()->format('h:i A') }}</span>
            </div>
        </div>
    </div> -->

    {{-- QUICK ACTION TILES --}}
    <div class="quick-actions-grid">
        <a href="{{ route('sales.create') }}" class="action-tile">
            <div class="tile-icon bg-sale">
                <i class="fas fa-cash-register"></i>
            </div>
            <div class="tile-info">
                <span class="tile-title">New Sale</span>
                <span class="tile-desc">POS Counter</span>
            </div>
        </a>
        <a href="{{ route('purchase.create') }}" class="action-tile">
            <div class="tile-icon bg-purchase">
                <i class="fas fa-cart-plus"></i>
            </div>
            <div class="tile-info">
                <span class="tile-title">Purchase</span>
                <span class="tile-desc">Stock Inward</span>
            </div>
        </a>
        <a href="{{ route('items.index') }}" class="action-tile">
            <div class="tile-icon bg-medicine">
                <i class="fas fa-capsules"></i>
            </div>
            <div class="tile-info">
                <span class="tile-title">Medicines</span>
                <span class="tile-desc">Inventory</span>
            </div>
        </a>
        <a href="{{ route('customers.index') }}" class="action-tile">
            <div class="tile-icon bg-customer">
                <i class="fas fa-users"></i>
            </div>
            <div class="tile-info">
                <span class="tile-title">Customers</span>
                <span class="tile-desc">Patient Care</span>
            </div>
        </a>
        <a href="{{ route('suppliers.index') }}" class="action-tile">
            <div class="tile-icon bg-supplier">
                <i class="fas fa-truck"></i>
            </div>
            <div class="tile-info">
                <span class="tile-title">Suppliers</span>
                <span class="tile-desc">Vendors</span>
            </div>
        </a>
    </div>

    {{-- KPI CARDS ROW --}}
<div class="kpi-row">
    {{-- Total Revenue --}}
    <div class="kpi-card revenue">
        <div class="card-glow"></div>
        <div class="card-header">
            <div class="card-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <span class="card-badge">TOTAL REVENUE</span>
        </div>
        <div class="card-value">
            ₹ {{ number_format(($totalSalesAmount ?? 0) + ($totalOnlineRevenue ?? 0), 2) }}
        </div>
        <div class="card-footer">
            <span class="trend up"><i class="fas fa-arrow-up"></i> {{ $todaySalesTrend ?? 0 }}%</span>
            <span>Online + Offline</span>
        </div>
    </div>

    {{-- Offline Sales --}}
    <a href="{{ route('sales.index') }}" class="kpi-card offline">
        <div class="card-glow"></div>
        <div class="card-header">
            <div class="card-icon">
                <i class="fas fa-store"></i>
            </div>
            <span class="card-badge">OFFLINE SALES</span>
        </div>
        <div class="card-value">
            {{ number_format($totalOfflineOrders ?? 0) }}
        </div>
        <div class="card-footer">
            <span class="amount">₹ {{ number_format($totalSalesAmount ?? 0, 2) }}</span>
            <span>Counter</span>
        </div>
    </a>

    {{-- Online Orders --}}
    <a href="{{ route('orders.index') }}" class="kpi-card online">
        <div class="card-glow"></div>
        <div class="card-header">
            <div class="card-icon">
                <i class="fas fa-globe"></i>
            </div>
            <span class="card-badge">ONLINE ORDERS</span>
        </div>
        <div class="card-value">
            {{ number_format($totalOnlineOrders ?? 0) }}
        </div>
        <div class="card-footer">
            <span class="amount">₹ {{ number_format($totalOnlineRevenue ?? 0, 0) }}</span>
            <span>Website</span>
        </div>
    </a>

    {{-- Inventory --}}
    <a href="{{ route('items.index') }}" class="kpi-card inventory">
        <div class="card-glow"></div>
        <div class="card-header">
            <div class="card-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <span class="card-badge">INVENTORY</span>
        </div>
        <div class="card-value">
            {{ number_format($totalStock ?? 0) }}
        </div>
        <div class="card-footer">
            <span class="alert">{{ $lowStockCount ?? 0 }} Low Stock</span>
            <span>Manage</span>
        </div>
    </a>

    {{-- Customers --}}
    <a href="{{ route('customers.index') }}" class="kpi-card customers">
        <div class="card-glow"></div>
        <div class="card-header">
            <div class="card-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <span class="card-badge">CUSTOMERS</span>
        </div>
        <div class="card-value">
            {{ number_format($totalCustomers ?? 0) }}
        </div>
        <div class="card-footer">
            <span class="new">{{ $todayCustomers ?? 0 }} New</span>
            <span>View All</span>
        </div>
    </a>
</div>

    {{-- MAIN CONTENT GRID --}}
    <div class="main-grid">
        {{-- LEFT COLUMN: CHARTS --}}
        <div class="grid-col charts-col">
            {{-- Revenue Chart --}}
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h3><i class="fas fa-chart-line"></i> Revenue Analytics</h3>
                        <p>Online vs Offline revenue trend</p>
                    </div>
                    <div class="live-indicator">
                        <span class="live-dot"></span> Live Data
                    </div>
                </div>
                <div class="mini-stats">
                    <div class="mini-stat">
                        <span class="mini-label">Total Revenue</span>
                        <strong>₹ {{ number_format($monthlyTotalSales ?? 0, 2) }}</strong>
                    </div>
                    <div class="mini-stat offline-stat">
                        <span class="mini-label">Offline Revenue</span>
                        <strong class="text-success">₹ {{ number_format($monthlyOfflineSales ?? 0, 2) }}</strong>
                    </div>
                    <div class="mini-stat online-stat">
                        <span class="mini-label">Online Revenue</span>
                        <strong class="text-primary">₹ {{ number_format($monthlyOnlineSales ?? 0, 2) }}</strong>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            {{-- Sales Distribution --}}
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h3><i class="fas fa-chart-pie"></i> Sales Distribution</h3>
                        <p>Revenue breakdown by channel</p>
                    </div>
                    <span class="month-badge">Monthly</span>
                </div>
                <div class="pie-container">
                    <canvas id="salesPieChart"></canvas>
                </div>
                <div class="pie-labels">
                    <span><i class="fas fa-circle" style="color: #10b981;"></i> Offline ({{ number_format(($monthlyOfflineSales ?? 0) / max(($monthlyTotalSales ?? 1), 1) * 100, 0) }}%)</span>
                    <span><i class="fas fa-circle" style="color: #3b82f6;"></i> Online ({{ number_format(($monthlyOnlineSales ?? 0) / max(($monthlyTotalSales ?? 1), 1) * 100, 0) }}%)</span>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: BUSINESS OVERVIEW --}}
        <div class="grid-col overview-col">
            <div class="overview-card">
                <div class="overview-header">
                    <h3><i class="fas fa-chart-simple"></i> Business Overview</h3>
                    <span class="smart-badge">Smart Insights</span>
                </div>
                <div class="overview-list">
                    <a href="{{ route('sales.index') }}" class="overview-row">
                        <div class="row-left">
                            <div class="row-icon bg-sale-light">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div>
                                <h4>Total Sales</h4>
                                <p>Pharmacy revenue</p>
                            </div>
                        </div>
                        <strong>₹ {{ number_format($totalSalesAmount ?? 0, 2) }}</strong>
                    </a>
                    <a href="{{ route('purchase.index') }}" class="overview-row">
                        <div class="row-left">
                            <div class="row-icon bg-purchase-light">
                                <i class="fas fa-cart-plus"></i>
                            </div>
                            <div>
                                <h4>Total Purchases</h4>
                                <p>Procurement value</p>
                            </div>
                        </div>
                        <strong>₹ {{ number_format($totalPurchaseAmount ?? 0, 2) }}</strong>
                    </a>
                    <a href="{{ route('customers.index') }}" class="overview-row">
                        <div class="row-left">
                            <div class="row-icon bg-customer-light">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h4>Active Customers</h4>
                                <p>Registered patients</p>
                            </div>
                        </div>
                        <strong>{{ number_format($totalCustomers ?? 0) }}</strong>
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="overview-row">
                        <div class="row-left">
                            <div class="row-icon bg-supplier-light">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div>
                                <h4>Suppliers</h4>
                                <p>Active vendors</p>
                            </div>
                        </div>
                        <strong>{{ number_format($totalSuppliers ?? 0) }}</strong>
                    </a>
                    <a href="{{ route('items.index') }}" class="overview-row">
                        <div class="row-left">
                            <div class="row-icon bg-medicine-light">
                                <i class="fas fa-capsules"></i>
                            </div>
                            <div>
                                <h4>Medicine SKUs</h4>
                                <p>Total products</p>
                            </div>
                        </div>
                        <strong>{{ number_format($totalMedicines ?? 0) }}</strong>
                    </a>
                </div>
            </div>

            {{-- Quick Stats Card --}}
            <div class="stats-card">
                <div class="stats-header">
                    <i class="fas fa-bolt"></i>
                    <span>Pharmacy Health Score</span>
                </div>
                <div class="stats-value">92<span>/100</span></div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 92%"></div>
                </div>
                <div class="stats-footer">
                    <span><i class="fas fa-check-circle"></i> Inventory optimized</span>
                    <span><i class="fas fa-chart-line"></i> Sales growing</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* ========== PHARMASPHERE 360 PREMIUM DASHBOARD ========== */
    @import url('https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap');

    .pharma-dashboard-wrapper {
        padding: 24px 28px;
        background: linear-gradient(145deg, #eef2f9 0%, #e6ecf5 100%);
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }

    /* Header */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 32px;
        background: rgba(255,255,255,0.75);
        backdrop-filter: blur(15px);
        padding: 18px 28px;
        border-radius: 28px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        border: 1px solid rgba(255,255,255,0.6);
    }

    .brand-logo {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 26px;
        font-weight: 800;
        background: linear-gradient(135deg, #0b2b5c, #1e4a76);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
    .brand-logo i {
        background: linear-gradient(135deg, #0b2b5c, #1e4a76);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        font-size: 32px;
    }
    .brand-360 {
        background: linear-gradient(135deg, #059669, #10b981);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
    .brand-tagline {
        font-size: 12px;
        color: #5a6e8a;
        margin-top: 4px;
        margin-left: 45px;
    }

    .header-right {
        display: flex;
        gap: 20px;
        align-items: center;
    }
    .filter-group {
        background: white;
        padding: 8px 18px;
        border-radius: 40px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .filter-group i { color: #3b82f6; }
    .filter-select {
        border: none;
        background: transparent;
        font-weight: 500;
        outline: none;
        cursor: pointer;
    }
    .date-time {
        background: white;
        padding: 8px 20px;
        border-radius: 40px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        color: #1e293b;
    }
    .time-sep { color: #cbd5e1; }

    /* Quick Actions Grid */
    .quick-actions-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 35px;
    }
    .action-tile {
        flex: 1;
        min-width: 160px;
        background: white;
        border-radius: 24px;
        padding: 18px 16px;
        display: flex;
        align-items: center;
        gap: 18px;
        text-decoration: none;
        transition: all 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        box-shadow: 0 8px 20px rgba(0,0,0,0.04);
        border: 1px solid rgba(255,255,255,0.5);
    }
    .action-tile:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 35px rgba(0,0,0,0.1);
    }
    .tile-icon {
        width: 55px;
        height: 55px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: white;
    }
    .bg-sale { background: linear-gradient(135deg, #10b981, #059669); }
    .bg-purchase { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .bg-medicine { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .bg-customer { background: linear-gradient(135deg, #06b6d4, #0891b2); }
    .bg-supplier { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .tile-title { font-weight: 700; color: #0f172a; font-size: 16px; display: block; }
    .tile-desc { font-size: 11px; color: #6c7a91; }

    /* ========== KPI ROW - SMALLER & SMOOTHER CARDS ========== */
    .kpi-row {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 30px;
    }

    .kpi-card {
        flex: 1;
        min-width: 180px;
        background: white;
        border-radius: 20px;
        padding: 16px 18px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .card-glow {
        position: absolute;
        top: -30%;
        right: -20%;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .card-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        background: rgba(0,0,0,0.04);
    }

    .card-badge {
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: #6c7a91;
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .card-value {
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 12px;
        line-height: 1.2;
    }

    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
        color: #6c7a91;
        border-top: 1px solid #eef2f6;
        padding-top: 10px;
    }

    .trend.up {
        color: #10b981;
        font-weight: 600;
        font-size: 11px;
    }

    .amount {
        font-weight: 600;
        font-size: 12px;
    }

    .alert {
        color: #f59e0b;
        font-weight: 600;
        font-size: 11px;
    }

    .new {
        color: #06b6d4;
        font-weight: 600;
        font-size: 11px;
    }

    /* Card Color Variants */
    .revenue {
        border-bottom: 3px solid #10b981;
        background: linear-gradient(135deg, #ffffff, #f0fdf4);
    }

    .offline {
        border-bottom: 3px solid #059669;
        background: linear-gradient(135deg, #ffffff, #ecfdf5);
    }

    .online {
        border-bottom: 3px solid #3b82f6;
        background: linear-gradient(135deg, #ffffff, #eff6ff);
    }

    .inventory {
        border-bottom: 3px solid #f59e0b;
        background: linear-gradient(135deg, #ffffff, #fffbeb);
    }

    .customers {
        border-bottom: 3px solid #06b6d4;
        background: linear-gradient(135deg, #ffffff, #ecfeff);
    }

    /* Icon Colors */
    .revenue .card-icon { color: #10b981; background: rgba(16, 185, 129, 0.1); }
    .offline .card-icon { color: #059669; background: rgba(5, 150, 105, 0.1); }
    .online .card-icon { color: #3b82f6; background: rgba(59, 130, 246, 0.1); }
    .inventory .card-icon { color: #f59e0b; background: rgba(245, 158, 11, 0.1); }
    .customers .card-icon { color: #06b6d4; background: rgba(6, 182, 212, 0.1); }

    /* Main Grid */
    .main-grid {
        display: grid;
        grid-template-columns: 1fr 0.9fr;
        gap: 28px;
    }

    /* Chart Card */
    .chart-card {
        background: white;
        border-radius: 32px;
        padding: 24px;
        margin-bottom: 28px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.05);
        transition: all 0.3s;
    }
    .chart-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 35px rgba(0,0,0,0.08);
    }
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .chart-header h3 {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: #0f172a;
    }
    .chart-header h3 i { margin-right: 10px; color: #3b82f6; }
    .chart-header p { font-size: 12px; color: #6c7a91; margin: 4px 0 0; }
    .live-indicator {
        background: #d1fae5;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
        color: #065f46;
    }
    .live-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        animation: pulse 1.5s infinite;
        margin-right: 6px;
    }
    @keyframes pulse {
        0% { opacity: 0.4; transform: scale(0.8); }
        100% { opacity: 1; transform: scale(1.2); }
    }

    .mini-stats {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
    }
    .mini-stat {
        flex: 1;
        background: #f8fafc;
        padding: 14px;
        border-radius: 20px;
        text-align: center;
        transition: all 0.2s;
    }
    .mini-stat:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
    }
    .mini-label { font-size: 11px; color: #6c7a91; display: block; margin-bottom: 6px; }
    .mini-stat strong { font-size: 18px; }
    .offline-stat { border-left: 3px solid #10b981; }
    .online-stat { border-left: 3px solid #3b82f6; }
    .chart-container { height: 280px; }
    .pie-container { height: 220px; position: relative; }
    .pie-labels {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 20px;
        font-size: 13px;
        font-weight: 500;
    }
    .month-badge {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        color: white;
        padding: 6px 16px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
    }

    /* Overview Card */
    .overview-card {
        background: white;
        border-radius: 32px;
        overflow: hidden;
        margin-bottom: 28px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.05);
        transition: all 0.3s;
    }
    .overview-card:hover {
        box-shadow: 0 16px 35px rgba(0,0,0,0.08);
    }
    .overview-header {
        padding: 22px 24px;
        border-bottom: 1px solid #eef2f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .overview-header h3 {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }
    .overview-header h3 i { margin-right: 10px; color: #3b82f6; }
    .smart-badge {
        background: #fef3c7;
        color: #d97706;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 700;
    }
    .overview-list {
        display: flex;
        flex-direction: column;
    }
    .overview-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 24px;
        text-decoration: none;
        border-bottom: 1px solid #f0f2f6;
        transition: all 0.25s;
    }
    .overview-row:hover { background: #f8fafc; padding-left: 30px; }
    .row-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .row-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .bg-sale-light { background: #d1fae5; color: #059669; }
    .bg-purchase-light { background: #dbeafe; color: #2563eb; }
    .bg-customer-light { background: #cffafe; color: #0891b2; }
    .bg-supplier-light { background: #fee2e2; color: #dc2626; }
    .bg-medicine-light { background: #fed7aa; color: #d97706; }
    .overview-row h4 { font-size: 15px; font-weight: 700; margin: 0; color: #1e293b; }
    .overview-row p { font-size: 11px; margin: 2px 0 0; color: #6c7a91; }
    .overview-row strong { font-size: 16px; color: #0f172a; }

    /* Stats Card */
    .stats-card {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        border-radius: 32px;
        padding: 24px;
        color: white;
        transition: all 0.3s;
    }
    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 35px rgba(0,0,0,0.15);
    }
    .stats-header {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .stats-value {
        font-size: 48px;
        font-weight: 800;
        margin-bottom: 15px;
    }
    .stats-value span { font-size: 18px; opacity: 0.7; }
    .progress-bar {
        background: rgba(255,255,255,0.2);
        border-radius: 30px;
        height: 8px;
        margin: 15px 0;
        overflow: hidden;
    }
    .progress-fill {
        background: #10b981;
        height: 100%;
        border-radius: 30px;
        width: 0%;
        transition: width 0.5s ease;
    }
    .stats-footer {
        display: flex;
        gap: 20px;
        font-size: 12px;
        margin-top: 18px;
        opacity: 0.9;
    }

    /* Text Utilities */
    .text-success { color: #10b981 !important; }
    .text-primary { color: #3b82f6 !important; }

    /* Responsive Design */
    @media (max-width: 1100px) {
        .main-grid { grid-template-columns: 1fr; }
        .pharma-dashboard-wrapper { padding: 18px; }
        .dashboard-header { flex-direction: column; align-items: flex-start; }
    }

    @media (max-width: 768px) {
        .kpi-card {
            min-width: calc(50% - 16px);
        }
        .card-value {
            font-size: 22px;
        }
        .quick-actions-grid {
            gap: 12px;
        }
        .action-tile {
            min-width: calc(50% - 12px);
            padding: 14px 12px;
        }
        .tile-icon {
            width: 45px;
            height: 45px;
            font-size: 20px;
        }
    }

    @media (max-width: 480px) {
        .kpi-card {
            min-width: 100%;
        }
        .action-tile {
            min-width: 100%;
        }
        .mini-stats {
            flex-direction: column;
        }
        .pie-labels {
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .stats-footer {
            flex-direction: column;
            gap: 8px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Line Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels ?? []) !!},
            datasets: [{
                label: 'Offline Sales',
                data: {!! json_encode($offlineChartData ?? []) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#10b981',
                pointRadius: 4,
                pointHoverRadius: 7
            }, {
                label: 'Online Orders',
                data: {!! json_encode($onlineChartData ?? []) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.05)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#3b82f6',
                pointRadius: 4,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } }
        }
    });

    // Pie Chart
    const pieCtx = document.getElementById('salesPieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Offline', 'Online'],
            datasets: [{
                data: [{{ $monthlyOfflineSales ?? 0 }}, {{ $monthlyOnlineSales ?? 0 }}],
                backgroundColor: ['#10b981', '#3b82f6'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endpush