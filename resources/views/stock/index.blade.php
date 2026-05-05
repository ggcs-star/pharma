@extends('layouts.master')

@section('content')
<div class="saas-stock-page">
    {{-- Header Section --}}
    <div class="dashboard-header">
        <div>
            <h1 class="dashboard-title">Stock Management</h1>
            <p class="dashboard-subtitle">Track inventory with batch-wise details & customer purchase history</p>
        </div>
        <div class="quick-actions">
            <button class="btn-icon" title="Export data">
                <i class="bi bi-download"></i>
            </button>
            <button class="btn-icon" title="Settings">
                <i class="bi bi-gear"></i>
            </button>
        </div>
    </div>

    {{-- Summary Stats Cards (computed from existing data) --}}
    @php
        $totalProducts = $stocks->total();
        $totalStockSum = 0;
        $lowStockCount = 0;
        $outOfStockCount = 0;
        
        foreach($stocks as $stock) {
            $avail = collect($batchStocks[$stock->id] ?? [])->sum('stock');
            $totalStockSum += $avail;
            if($avail == 0) {
                $outOfStockCount++;
            } elseif($avail <= 10) {
                $lowStockCount++;
            }
        }
    @endphp
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ number_format($totalProducts) }}</span>
                <span class="stat-label">Total Products</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stock">
                <i class="bi bi-capsule"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ number_format($totalStockSum) }}</span>
                <span class="stat-label">Total Stock (Units)</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ number_format($lowStockCount) }}</span>
                <span class="stat-label">Low Stock Items</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ number_format($outOfStockCount) }}</span>
                <span class="stat-label">Out of Stock</span>
            </div>
        </div>
    </div>

    {{-- Workflow Steps (compact) --}}
    <div class="workflow-compact">
        <div class="step-item">
            <span class="step-number">1</span>
            <span class="step-name">Purchase Entry</span>
        </div>
        <i class="bi bi-arrow-right step-arrow"></i>
        <div class="step-item">
            <span class="step-number">2</span>
            <span class="step-name">MRP Update</span>
        </div>
        <i class="bi bi-arrow-right step-arrow"></i>
        <div class="step-item">
            <span class="step-number">3</span>
            <span class="step-name">Sales Entry</span>
        </div>
        <i class="bi bi-arrow-right step-arrow"></i>
        <div class="step-item">
            <span class="step-number">4</span>
            <span class="step-name">Stock Return</span>
        </div>
    </div>

    {{-- Filter Section (Card Style) --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('stock.index') }}" class="filter-form">
            <div class="filter-group search-group">
                <i class="bi bi-search"></i>
                <input type="text" name="search" placeholder="Search by product name or code..." value="{{ request('search') }}">
            </div>
            <div class="filter-group date-group">
                <i class="bi bi-calendar3"></i>
                <input type="date" name="from_date" value="{{ request('from_date') }}" placeholder="From">
                <span class="date-sep">—</span>
                <input type="date" name="to_date" value="{{ request('to_date') }}" placeholder="To">
            </div>
            <button type="submit" class="btn-primary">
                <i class="bi bi-funnel"></i> Apply
            </button>
            @if(request('search') || request('from_date') || request('to_date'))
                <a href="{{ route('stock.index') }}" class="btn-clear">
                    <i class="bi bi-x"></i> Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Stock Table (Enhanced) --}}
    <div class="table-wrapper">
        <table class="stock-table-modern">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-right">Purchased</th>
                    <th class="text-right">Returned</th>
                    <th class="text-right">Sold</th>
                    <th class="text-right">Available</th>
                    <th class="text-center">Batches</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                @php
                    $hasBatches = count($batchStocks[$stock->id] ?? []) > 0;
                    $batchCount = count($batchStocks[$stock->id] ?? []);
                    $totalPurchased = $stock->total_purchase ?? 0;
                    $totalReturned = abs($stock->total_return ?? 0);
                    $offlineSold = $stock->total_sale ?? 0;
                    $onlineSold = collect($batchStocks[$stock->id] ?? [])->sum('total_online_sale');
                    $availableStrip = collect($batchStocks[$stock->id] ?? [])->sum('stock');
                    $availableLoose = collect($batchStocks[$stock->id] ?? [])->sum('loose_stock');
                    
                    $packingText = strtolower($stock->packaging_detail ?? '');
                    if (str_contains($packingText, 'strip')) $unit = 'Strip';
                    elseif (str_contains($packingText, 'tube')) $unit = 'Tube';
                    elseif (str_contains($packingText, 'bottle')) $unit = 'Bottle';
                    elseif (str_contains($packingText, 'vial')) $unit = 'Vial';
                    else $unit = 'Unit';
                    
                    $stockLevel = $availableStrip > 50 ? 'high' : ($availableStrip > 10 ? 'medium' : ($availableStrip > 0 ? 'low' : 'out'));
                    $stockPercent = $availableStrip > 0 ? min(100, ($availableStrip / max(1, $totalPurchased)) * 100) : 0;
                @endphp
                
                <tr class="product-row" data-id="{{ $stock->id }}">
                    <td class="product-cell-modern">
                        <div class="product-info">
                            <div class="product-avatar">
                                @if($stock->main_image_url)
                                    <img src="{{ $stock->main_image_url }}" alt="{{ $stock->name }}">
                                @else
                                    <i class="bi bi-capsule"></i>
                                @endif
                            </div>
                            <div class="product-details">
                                <a href="{{ route('stock.show', $stock->id) }}" class="product-name">{{ $stock->name }}</a>
                                <div class="product-meta">
                                    <code class="product-code">{{ $stock->code ?? 'N/A' }}</code>
                                    <span class="packaging-badge">{{ $unit }}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-right">
                        <span class="stat-badge purchased">{{ number_format($totalPurchased) }}</span>
                    </td>
                    <td class="text-right">
                        <span class="stat-badge returned">{{ number_format($totalReturned) }}</span>
                    </td>
                    <td class="text-right">
                        <div class="sold-stats">
                            <span class="stat-badge sold">{{ number_format($offlineSold) }}</span>
                            @if($onlineSold > 0)
                                <span class="online-badge">+{{ number_format($onlineSold) }} online</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-right">
                        <div class="stock-cell-modern">
                            <div class="stock-number">
                                <span class="stock-value stock-{{ $stockLevel }}">{{ number_format($availableStrip) }}</span>
                                <span class="stock-unit">{{ $unit }}</span>
                            </div>
                            @if($availableLoose > 0)
                                <div class="loose-stock">+{{ number_format($availableLoose) }} loose</div>
                            @endif
                            <div class="progress-bar-modern">
                                <div class="progress-fill fill-{{ $stockLevel }}" style="width: {{ $stockPercent }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        @if($hasBatches)
                            <span class="batch-indicator">
                                <i class="bi bi-layers"></i> {{ $batchCount }}
                            </span>
                        @else
                            <span class="no-batches">—</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('stock.show', $stock->id) }}" class="btn-view">
                            <i class="bi bi-eye"></i> View
                        </a>
                    </td>
                </tr>
                
                @empty
                <tr>
                    <td colspan="7" class="empty-state-modern">
                        <div class="empty-content">
                            <i class="bi bi-box-seam"></i>
                            <h5>No stock items found</h5>
                            <p>Try adjusting your search filters</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        {{-- Pagination --}}
        @if($stocks->hasPages())
        <div class="pagination-modern">
            <div class="pagination-info">
                Showing {{ $stocks->firstItem() ?? 0 }} to {{ $stocks->lastItem() ?? 0 }} of {{ $stocks->total() }}
            </div>
            {{ $stocks->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<style>
/* Modern SaaS Dashboard Styles */
:root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --primary-light: #a5b4fc;
    --success: #10b981;
    --success-light: #d1fae5;
    --warning: #f59e0b;
    --warning-light: #fef3c7;
    --danger: #ef4444;
    --danger-light: #fee2e2;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    --radius-md: 0.75rem;
    --radius-lg: 1rem;
}

.saas-stock-page {
    max-width: 1600px;
    margin: 0 auto;
    padding: 1.5rem;
    background: var(--gray-50);
    min-height: 100vh;
}

/* Dashboard Header */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.dashboard-title {
    font-size: 1.875rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--gray-800) 0%, var(--gray-600) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0 0 0.25rem 0;
}

.dashboard-subtitle {
    color: var(--gray-500);
    font-size: 0.875rem;
    margin: 0;
}

.quick-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-icon {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 0.5rem;
    padding: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
    color: var(--gray-500);
}

.btn-icon:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
    color: var(--gray-700);
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: white;
    border-radius: var(--radius-md);
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-200);
    transition: all 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--gray-300);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-icon.total {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
}

.stat-icon.stock {
    background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
    color: white;
}

.stat-icon.warning {
    background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
    color: white;
}

.stat-icon.danger {
    background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
    color: white;
}

.stat-info {
    flex: 1;
}

.stat-value {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--gray-800);
    display: block;
    line-height: 1.2;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Workflow Compact */
.workflow-compact {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

.step-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.5rem;
}

.step-number {
    width: 24px;
    height: 24px;
    background: var(--gray-100);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--gray-600);
}

.step-name {
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--gray-700);
}

.step-arrow {
    color: var(--gray-300);
    font-size: 0.75rem;
}

/* Filter Card */
.filter-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    padding: 1rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-sm);
}

.filter-form {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    align-items: center;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    transition: all 0.2s;
}

.filter-group:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 2px var(--primary-light);
}

.search-group {
    flex: 2;
    min-width: 200px;
}

.search-group i, .date-group i {
    color: var(--gray-400);
}

.filter-group input {
    border: none;
    background: transparent;
    font-size: 0.875rem;
    outline: none;
    flex: 1;
}

.date-group {
    flex: 1;
    min-width: 240px;
}

.date-sep {
    color: var(--gray-400);
    font-size: 0.75rem;
}

.btn-primary {
    background: var(--primary);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.btn-clear {
    background: var(--gray-100);
    color: var(--gray-600);
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.btn-clear:hover {
    background: var(--gray-200);
    color: var(--gray-800);
}

/* Table Wrapper */
.table-wrapper {
    background: white;
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
    overflow-x: auto;
    box-shadow: var(--shadow-sm);
}

/* Modern Table */
.stock-table-modern {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

.stock-table-modern th {
    text-align: left;
    padding: 1rem;
    background: var(--gray-50);
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--gray-500);
    border-bottom: 1px solid var(--gray-200);
}

.stock-table-modern td {
    padding: 1rem;
    border-bottom: 1px solid var(--gray-100);
    font-size: 0.875rem;
}

.stock-table-modern tbody tr:hover {
    background: var(--gray-50);
}

.text-right {
    text-align: right;
}

.text-center {
    text-align: center;
}

/* Product Cell Modern */
.product-cell-modern {
    min-width: 280px;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.product-avatar {
    width: 44px;
    height: 44px;
    background: var(--gray-100);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.product-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-avatar i {
    font-size: 1.25rem;
    color: var(--gray-400);
}

.product-details {
    flex: 1;
}

.product-name {
    font-weight: 600;
    color: var(--gray-800);
    text-decoration: none;
    font-size: 0.875rem;
    display: block;
    margin-bottom: 0.25rem;
}

.product-name:hover {
    color: var(--primary);
}

.product-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.product-code {
    font-size: 0.7rem;
    color: var(--gray-400);
    background: var(--gray-50);
    padding: 0.125rem 0.375rem;
    border-radius: 0.25rem;
}

.packaging-badge {
    font-size: 0.65rem;
    padding: 0.125rem 0.5rem;
    background: var(--gray-100);
    color: var(--gray-600);
    border-radius: 1rem;
}

/* Stat Badges */
.stat-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.stat-badge.purchased {
    background: rgba(99, 102, 241, 0.1);
    color: var(--primary);
}

.stat-badge.returned {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.stat-badge.sold {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.sold-stats {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
}

.online-badge {
    font-size: 0.65rem;
    color: var(--success);
}

/* Stock Cell Modern */
.stock-cell-modern {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
}

.stock-number {
    display: flex;
    align-items: baseline;
    gap: 0.25rem;
}

.stock-value {
    font-size: 1rem;
    font-weight: 600;
}

.stock-value.stock-high { color: var(--success); }
.stock-value.stock-medium { color: var(--warning); }
.stock-value.stock-low { color: var(--danger); }
.stock-value.stock-out { color: var(--gray-400); }

.stock-unit {
    font-size: 0.7rem;
    color: var(--gray-400);
}

.loose-stock {
    font-size: 0.7rem;
    color: var(--gray-500);
}

.progress-bar-modern {
    width: 80px;
    height: 4px;
    background: var(--gray-200);
    border-radius: 2px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    transition: width 0.3s ease;
    border-radius: 2px;
}

.progress-fill.fill-high { background: var(--success); }
.progress-fill.fill-medium { background: var(--warning); }
.progress-fill.fill-low { background: var(--danger); }
.progress-fill.fill-out { background: var(--gray-400); }

/* Batch Indicator */
.batch-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    background: var(--gray-100);
    border-radius: 1rem;
    font-size: 0.7rem;
    font-weight: 500;
    color: var(--gray-600);
}

.no-batches {
    color: var(--gray-400);
    font-size: 0.75rem;
}

/* View Button */
.btn-view {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    background: var(--gray-100);
    color: var(--gray-600);
    text-decoration: none;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    transition: all 0.2s;
    border: 1px solid transparent;
}

.btn-view:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
}

/* Empty State */
.empty-state-modern {
    text-align: center;
    padding: 3rem;
}

.empty-content i {
    font-size: 3rem;
    color: var(--gray-300);
    margin-bottom: 1rem;
    display: block;
}

.empty-content h5 {
    color: var(--gray-600);
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
}

.empty-content p {
    color: var(--gray-400);
    font-size: 0.875rem;
}

/* Pagination Modern */
.pagination-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-top: 1px solid var(--gray-200);
    background: var(--gray-50);
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-info {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.pagination-modern .pagination {
    margin: 0;
}

.pagination-modern .page-link {
    border: none;
    margin: 0 0.15rem;
    border-radius: 0.5rem;
    padding: 0.35rem 0.7rem;
    color: var(--gray-600);
    font-size: 0.8rem;
    transition: all 0.2s;
}

.pagination-modern .page-link:hover {
    background: var(--gray-200);
    color: var(--gray-800);
}

.pagination-modern .page-item.active .page-link {
    background: var(--primary);
    color: white;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .saas-stock-page {
        padding: 1rem;
    }
    
    .dashboard-title {
        font-size: 1.5rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .workflow-compact {
        display: none;
    }
    
    .filter-form {
        flex-direction: column;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .date-group {
        min-width: auto;
    }
    
    .btn-primary, .btn-clear {
        width: 100%;
        justify-content: center;
    }
    
    .stock-table-modern th:nth-child(3),
    .stock-table-modern td:nth-child(3) {
        display: none;
    }
    
    .pagination-modern {
        flex-direction: column;
    }
}

@media (max-width: 640px) {
    .stock-table-modern th:nth-child(4),
    .stock-table-modern td:nth-child(4) {
        display: none;
    }
    
    .product-cell-modern {
        min-width: auto;
    }
}
</style>

@if(!isset($hasBootstrapIcons))
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endif
@endsection