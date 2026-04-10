@extends('supplier.layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    /* Dashboard Styles */
    .dashboard-header {
        margin-bottom: 28px;
    }

    .dashboard-header h2 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 6px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .dashboard-header h2 i {
        color: #0ea5e9;
        font-size: 1.8rem;
    }

    .dashboard-header p {
        color: #64748b;
        font-size: 0.9rem;
        margin: 0;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: white;
        border-radius: 24px;
        padding: 20px;
        border: 1px solid #eef2ff;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #0ea5e9, #3b82f6);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -12px rgba(0, 0, 0, 0.15);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background: #e0f2fe;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }

    .stat-icon i {
        font-size: 1.5rem;
        color: #0ea5e9;
    }

    .stat-info h6 {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin: 0 0 8px 0;
    }

    .stat-info h3 {
        font-size: 1.8rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .stat-info .trend {
        font-size: 0.7rem;
        margin-top: 6px;
        display: inline-block;
    }

    .trend-up {
        color: #10b981;
    }

    .trend-down {
        color: #ef4444;
    }

    /* Secondary Stats Grid */
    .stats-secondary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card-secondary {
        border-radius: 24px;
        padding: 20px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card-secondary:hover {
        transform: translateY(-4px);
    }

    .stat-card-secondary.warning {
        background: linear-gradient(135deg, #fef3c7, #fffbeb);
        border: 1px solid #fde68a;
    }

    .stat-card-secondary.danger {
        background: linear-gradient(135deg, #fee2e2, #fef2f2);
        border: 1px solid #fecaca;
    }

    .stat-card-secondary.success {
        background: linear-gradient(135deg, #dcfce7, #f0fdf4);
        border: 1px solid #bbf7d0;
    }

    .stat-card-secondary .stat-icon {
        background: rgba(255, 255, 255, 0.5);
    }

    .stat-card-secondary.warning .stat-info h6 {
        color: #92400e;
    }

    .stat-card-secondary.warning .stat-info h3 {
        color: #b45309;
    }

    .stat-card-secondary.danger .stat-info h6 {
        color: #991b1b;
    }

    .stat-card-secondary.danger .stat-info h3 {
        color: #dc2626;
    }

    .stat-card-secondary.success .stat-info h6 {
        color: #166534;
    }

    .stat-card-secondary.success .stat-info h3 {
        color: #15803d;
    }

    /* Recent Activity Table */
    .recent-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #eef2ff;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    .recent-header {
        padding: 20px 24px;
        background: #fafcff;
        border-bottom: 1px solid #eef2ff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .recent-header h5 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .recent-header h5 i {
        color: #0ea5e9;
    }

    .recent-badge {
        background: #e0f2fe;
        color: #0284c7;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .activity-table {
        width: 100%;
        border-collapse: collapse;
    }

    .activity-table thead tr {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .activity-table th {
        padding: 14px 20px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #475569;
        text-align: left;
    }

    .activity-table td {
        padding: 14px 20px;
        font-size: 0.85rem;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .activity-table tbody tr:hover {
        background: #f8fafc;
    }

    .item-name {
        font-weight: 600;
        color: #0f172a;
    }

    .item-batch {
        font-size: 0.7rem;
        color: #94a3b8;
        font-family: monospace;
        margin-top: 2px;
    }

    .badge-type {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 700;
    }

    .badge-purchase {
        background: #dcfce7;
        color: #15803d;
    }

    .badge-sale {
        background: #fee2e2;
        color: #b91c1c;
    }

    .qty-positive {
        color: #10b981;
        font-weight: 700;
    }

    .qty-negative {
        color: #ef4444;
        font-weight: 700;
    }

    .date-cell {
        font-size: 0.75rem;
        color: #64748b;
    }

    .date-cell i {
        margin-right: 4px;
        font-size: 0.7rem;
    }

    .empty-state-table {
        text-align: center;
        padding: 40px;
        color: #94a3b8;
    }

    .empty-state-table i {
        font-size: 3rem;
        margin-bottom: 12px;
        display: block;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .stats-secondary {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        
        .stats-secondary {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        
        .recent-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .activity-table {
            min-width: 500px;
        }
        
        .recent-card {
            overflow-x: auto;
        }
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stat-card, .stat-card-secondary, .recent-card {
        animation: fadeInUp 0.4s ease forwards;
    }

    .stat-card:nth-child(1) { animation-delay: 0s; }
    .stat-card:nth-child(2) { animation-delay: 0.05s; }
    .stat-card:nth-child(3) { animation-delay: 0.1s; }
    .stat-card:nth-child(4) { animation-delay: 0.15s; }
</style>

<div class="dashboard-header">
    <h2>
        <i class="fa fa-chart-line"></i>
        Dashboard
    </h2>
    <p>Welcome back! Here's an overview of your inventory and sales</p>
</div>

<!-- Primary Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa fa-capsules"></i>
        </div>
        <div class="stat-info">
            <h6>Total Items</h6>
            <h3>{{ $totalItems }}</h3>
            <span class="trend trend-up">
                <i class="fa fa-cube"></i> Active products
            </span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa fa-cubes"></i>
        </div>
        <div class="stat-info">
            <h6>Total Stock</h6>
            <h3>{{ number_format($totalStock) }}</h3>
            <span class="trend">
                <i class="fa fa-boxes"></i> Units in inventory
            </span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa fa-arrow-down"></i>
        </div>
        <div class="stat-info">
            <h6>Total Purchase</h6>
            <h3 class="trend-up">+{{ number_format($totalPurchase) }}</h3>
            <span class="trend trend-up">
                <i class="fa fa-chart-line"></i> Stock received
            </span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa fa-arrow-up"></i>
        </div>
        <div class="stat-info">
            <h6>Total Sold</h6>
            <h3 class="trend-down">-{{ number_format($totalSale) }}</h3>
            <span class="trend trend-down">
                <i class="fa fa-chart-line"></i> Stock sold
            </span>
        </div>
    </div>
</div>

<!-- Secondary Stats Grid -->
<div class="stats-secondary">
    <div class="stat-card-secondary warning">
        <div class="stat-icon">
            <i class="fa fa-exclamation-triangle"></i>
        </div>
        <div class="stat-info">
            <h6>Low Stock Items</h6>
            <h3>{{ $lowStock }}</h3>
            <span class="trend">Items below 10 units</span>
        </div>
    </div>

    <div class="stat-card-secondary danger">
        <div class="stat-icon">
            <i class="fa fa-calendar-times"></i>
        </div>
        <div class="stat-info">
            <h6>Expired Batches</h6>
            <h3>{{ $expired }}</h3>
            <span class="trend">Need immediate attention</span>
        </div>
    </div>

    <div class="stat-card-secondary success">
        <div class="stat-icon">
            <i class="fa fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <h6>Active Catalogs</h6>
            <h3>{{ $totalItems }}</h3>
            <span class="trend">All catalogs active</span>
        </div>
    </div>
</div>

<!-- Recent Stock Activity -->
<div class="recent-card">
    <div class="recent-header">
        <h5>
            <i class="fa fa-history"></i>
            Recent Stock Activity
        </h5>
        <span class="recent-badge">
            <i class="fa fa-clock"></i> Last 10 transactions
        </span>
    </div>

    <div class="table-responsive">
        <table class="activity-table">
            <thead>
                <tr>
                    <th>Item Details</th>
                    <th>Transaction Type</th>
                    <th>Quantity</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentStocks as $stock)
                    <tr>
                        <td>
                            <div class="item-name">
                                {{ $stock->catalog->item->name ?? 'N/A' }}
                            </div>
                            @if($stock->catalog->batch_no)
                                <div class="item-batch">
                                    <i class="fa fa-barcode"></i> Batch: {{ $stock->catalog->batch_no }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($stock->type == 'purchase')
                                <span class="badge-type badge-purchase">
                                    <i class="fa fa-arrow-down"></i> Stock In (Purchase)
                                </span>
                            @elseif($stock->type == 'sale')
                                <span class="badge-type badge-sale">
                                    <i class="fa fa-arrow-up"></i> Stock Out (Sale)
                                </span>
                            @elseif($stock->type == 'inbound')
                                <span class="badge-type" style="background:#dbeafe; color:#1e40af;">
                                    <i class="fa fa-arrow-down"></i> Stock In
                                </span>
                            @elseif($stock->type == 'outbound')
                                <span class="badge-type" style="background:#fef3c7; color:#b45309;">
                                    <i class="fa fa-arrow-up"></i> Stock Out
                                </span>
                            @else
                                <span class="badge-type" style="background:#f1f5f9; color:#475569;">
                                    <i class="fa fa-edit"></i> {{ ucfirst($stock->type) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="{{ in_array($stock->type, ['purchase', 'inbound']) ? 'qty-positive' : 'qty-negative' }}">
                                {{ in_array($stock->type, ['purchase', 'inbound']) ? '+' : '-' }}{{ $stock->qty }}
                            </span>
                            @if($stock->note)
                                <div class="item-batch" style="margin-top: 4px;">
                                    <i class="fa fa-sticky-note"></i> {{ Str::limit($stock->note, 30) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="date-cell">
                                <i class="fa fa-calendar-alt"></i> {{ $stock->created_at->format('d M, Y') }}
                            </div>
                            <div class="date-cell" style="margin-top: 4px;">
                                <i class="fa fa-clock"></i> {{ $stock->created_at->format('h:i A') }}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-state-table">
                            <i class="fa fa-chart-line"></i>
                            <p>No stock activity recorded yet</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection