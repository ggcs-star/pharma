@extends('layouts.master')

@section('content')
<div class="stock-page">
    {{-- Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Stock Management</h1>
            <p class="page-subtitle">Track inventory with batch-wise details & customer purchase history</p>
        </div>
        <div class="page-stats">
            <div class="stat-item">
                <i class="bi bi-box-seam"></i>
                <span>{{ $stocks->total() }} Products</span>
            </div>
        </div>
    </div>

    {{-- Workflow Steps --}}
    <div class="workflow-steps">
        <div class="step">
            <div class="step-number">1</div>
            <div class="step-content">
                <div class="step-title">Purchase Entry</div>
                <div class="step-desc">Stock In + Batch</div>
            </div>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-content">
                <div class="step-title">MRP Update</div>
                <div class="step-desc">Price + GST</div>
            </div>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-content">
                <div class="step-title">Sales Entry</div>
                <div class="step-desc">Strip/Loose Sales</div>
            </div>
        </div>
        <div class="step">
            <div class="step-number">4</div>
            <div class="step-content">
                <div class="step-title">Stock Return</div>
                <div class="step-desc">Adjustments</div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-section">
        <form method="GET" action="{{ route('stock.index') }}" class="filter-form">
            <div class="filter-search">
                <i class="bi bi-search"></i>
                <input type="text" name="search" placeholder="Search by name or code..." value="{{ request('search') }}">
            </div>
            <div class="filter-date">
                <i class="bi bi-calendar3"></i>
                <input type="date" name="from_date" value="{{ request('from_date') }}" placeholder="From">
                <span>—</span>
                <input type="date" name="to_date" value="{{ request('to_date') }}" placeholder="To">
            </div>
            <button type="submit" class="filter-btn">Apply</button>
            @if(request('search') || request('from_date') || request('to_date'))
                <a href="{{ route('stock.index') }}" class="clear-btn">Clear</a>
            @endif
        </form>
    </div>

    {{-- Stock Table --}}
    <div class="table-container">
        <table class="stock-table">
            <thead>
                <tr>
                    <th class="col-expand"></th>
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
                @endphp
                
                <tr class="parent-row" data-id="{{ $stock->id }}">
                    <td class="col-expand">
                        @if($hasBatches)
                            <button class="expand-btn" data-id="{{ $stock->id }}">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        @endif
                    </td>
                    <td>
                        <div class="product-cell">
                            @if($stock->main_image_url)
                                <img src="{{ $stock->main_image_url }}" class="product-img">
                            @else
                                <div class="product-img-placeholder">
                                    <i class="bi bi-capsule"></i>
                                </div>
                            @endif
                            <div>
                                <a href="{{ route('stock.show', $stock->id) }}" class="product-name">{{ $stock->name }}</a>
                                @if(isset($stock->code))
                                    <div class="product-code">{{ $stock->code }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-right">
                        <span class="stat-badge stat-in">{{ number_format($totalPurchased) }}</span>
                    </td>
                    <td class="text-right">
    <span class="stat-badge stat-return">
        {{ number_format($totalReturned) }}
    </span>
</td>
                    <td class="text-right">
                        <div>
                            <span class="stat-badge stat-out">{{ number_format($offlineSold) }}</span>
                            @if($onlineSold > 0)
                                <div class="stat-online">+{{ number_format($onlineSold) }} online</div>
                            @endif
                        </div>
                    </td>
                    <td class="text-right">
                        <div class="stock-cell">
                            <div class="stock-value">
                                <span class="stock-number stock-{{ $stockLevel }}">{{ number_format($availableStrip) }}</span>
                                <span class="stock-unit">{{ $unit }}</span>
                            </div>
                            @if($availableLoose > 0)
                                <div class="stock-loose">+{{ number_format($availableLoose) }} loose</div>
                            @endif
                            <div class="stock-bar">
                                <div class="stock-bar-fill stock-{{ $stockLevel }}" style="width: {{ $availableStrip > 0 ? min(100, ($availableStrip / max(1, $totalPurchased)) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        @if($hasBatches)
                            <span class="batch-badge">
                                <i class="bi bi-layers"></i> {{ $batchCount }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('stock.show', $stock->id) }}" class="action-link">
                            <i class="bi bi-eye"></i> Manage
                        </a>
                    </td>
                </tr>
                
                {{-- Batch Details Row with Customer Purchase History --}}
                @if($hasBatches)
                <tr class="child-row" id="batch-row-{{ $stock->id }}" style="display: none;">
                    <td colspan="7" class="batch-cell">
                        <div class="batch-wrapper">
                            {{-- Batch Header --}}
                            <div class="batch-header">
                                <i class="bi bi-layers"></i>
                                <span>Batch Details</span>
                                <span class="batch-count">{{ $batchCount }} batches</span>
                            </div>
                            
                            {{-- Batch List --}}
                            <div class="batch-list">
                                @foreach($batchStocks[$stock->id] ?? [] as $batch)
                                @php
                                    $expiryDate = isset($batch->expiry_date) ? \Carbon\Carbon::parse($batch->expiry_date) : null;
                                    $isExpired = $expiryDate ? $expiryDate->isPast() : false;
                                    $isNearExpiry = $expiryDate ? ($expiryDate->diffInDays(now()) <= 30 && !$isExpired) : false;
                                    
                                    // Get customer sales for this batch - ADJUST ACCORDING TO YOUR DATABASE STRUCTURE
                                    $offlineSales = isset($customerSales[$batch->id]['offline_sales']) ? $customerSales[$batch->id]['offline_sales'] : [];
                                    $onlineSales = isset($customerSales[$batch->id]['online_sales']) ? $customerSales[$batch->id]['online_sales'] : [];
                                    $hasCustomerHistory = count($offlineSales) > 0 || count($onlineSales) > 0;
                                @endphp
                                
                                <div class="batch-item">
                                    <div class="batch-code">
                                        <i class="bi bi-upc-scan"></i>
                                        <span>{{ $batch->batch_code ?? 'N/A' }}</span>
                                    </div>
                                    <div class="batch-stats">
                                        <div class="batch-stat">
                                            <span class="stat-label">Purchased</span>
                                            <strong>{{ number_format($batch->total_purchase ?? 0) }}</strong>
                                        </div>
                                        <div class="batch-stat">
                                            <span class="stat-label">Sold</span>
                                            <strong>{{ number_format(($batch->total_sale ?? 0) + ($batch->total_online_sale ?? 0)) }}</strong>
                                        </div>
                                        <div class="batch-stat">
                                            <span class="stat-label">Available</span>
                                            <strong>{{ number_format($batch->stock ?? 0) }} {{ $unit }}</strong>
                                            @if(($batch->loose_stock ?? 0) > 0)
                                                <small>+{{ number_format($batch->loose_stock) }} loose</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="batch-expiry">
                                        @if($expiryDate)
                                            <span class="expiry-badge {{ $isExpired ? 'expired' : ($isNearExpiry ? 'warning' : 'valid') }}">
                                                <i class="bi {{ $isExpired ? 'bi-x-circle' : ($isNearExpiry ? 'bi-clock' : 'bi-check-circle') }}"></i>
                                                {{ $expiryDate->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="expiry-badge">No expiry</span>
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- CUSTOMER PURCHASE HISTORY SECTION --}}
                                <div class="customer-history-section">
                                    <div class="history-header">
                                        <i class="bi bi-people"></i>
                                        <span>Customer Purchase History</span>
                                        <span class="history-count">{{ count($offlineSales) + count($onlineSales) }} transactions</span>
                                    </div>
                                    
                                    @if($hasCustomerHistory)
                                    <div class="customer-cards-grid">
                                        {{-- Offline Sales Cards --}}
                                        @foreach($offlineSales as $sale)
                                        <div class="customer-card offline">
                                            <div class="customer-card-header">
                                                <div class="customer-avatar offline-avatar">
                                                    <i class="bi bi-person-badge"></i>
                                                </div>
                                                <div class="customer-info">
                                                    <div class="customer-name">{{ $sale->customer_name ?? 'Walk-in Customer' }}</div>
                                                    <div class="customer-meta">
                                                        <span class="type-badge offline">Offline Sale</span>
                                                        @if(isset($sale->doctor_name) && $sale->doctor_name)
                                                            <span class="doctor-badge">
                                                                <i class="bi bi-hospital"></i> Dr. {{ $sale->doctor_name }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="order-status">
                                                    <span class="status-badge {{ strtolower($sale->status ?? 'completed') }}">
                                                        <i class="bi bi-check-circle-fill"></i>
{{ ucfirst((string) ($sale->status ?? 'Completed')) }}                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="customer-card-body">
                                                <div class="info-grid">
                                                    <div class="info-item">
                                                        <span class="info-label">Bill No:</span>
                                                        <span class="info-value highlight">{{ $sale->bill_no ?? 'N/A' }}</span>
                                                    </div>
                                                 <div class="info-item">
    <span class="info-label">Date & Time:</span>
    <span class="info-value">
        {{ isset($sale->created_at) ? \Carbon\Carbon::parse($sale->created_at)->format('d M Y, h:i A') : 'N/A' }}
    </span>
</div>
                                                </div>
                                                
                                                <div class="info-grid">
                                                    <div class="info-item">
                                                        <span class="info-label">Quantity:</span>
                                                        <span class="info-value quantity">
                                                            {{ number_format($sale->quantity ?? 0) }}
                                                            <small>{{ ucfirst($sale->sale_type ?? $unit) }}</small>
                                                        </span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Rate:</span>
                                                        <span class="info-value">₹{{ number_format($sale->selling_price ?? 0, 2) }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Total:</span>
                                                        <span class="info-value amount">₹{{ number_format($sale->total_amount ?? 0, 2) }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="info-grid">
                                                    <div class="info-item">
                                                        <span class="info-label">Payment Mode:</span>
                                                        <span class="payment-badge {{ strtolower($sale->payment_mode ?? 'cash') }}">
                                                            <i class="bi {{ $sale->payment_mode == 'upi' ? 'bi-phone' : ($sale->payment_mode == 'card' ? 'bi-credit-card' : 'bi-cash') }}"></i>
                                                            {{ ucfirst($sale->payment_mode ?? 'Cash') }}
                                                        </span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Batch Used:</span>
                                                        <span class="info-value batch-ref">{{ $batch->batch_code ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        
                                        {{-- Online Sales Cards --}}
                                        @foreach($onlineSales as $order)
                                        <div class="customer-card online">
                                            <div class="customer-card-header">
                                                <div class="customer-avatar online-avatar">
                                                    <i class="bi bi-globe2"></i>
                                                </div>
                                                <div class="customer-info">
                                                    <div class="customer-name">{{ $order->customer_name ?? 'Online Customer' }}</div>
                                                    <div class="customer-meta">
                                                        <span class="type-badge online">Online Order</span>
                                                        @if(isset($order->delivery_partner))
                                                            <span class="delivery-badge">
                                                                <i class="bi bi-truck"></i> {{ $order->delivery_partner }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="order-status">
                                                    <span class="status-badge {{ strtolower($order->order_status ?? 'delivered') }}">
                                                        <i class="bi bi-truck"></i>
                                                        {{ ucfirst($order->order_status ?? 'Delivered') }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="customer-card-body">
                                                <div class="info-grid">
                                                    <div class="info-item">
                                                        <span class="info-label">Order No:</span>
                                                        <span class="info-value highlight">{{ $order->order_no ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Date & Time:</span>
                                                        <span class="info-value">{{ isset($order->order_date) ? \Carbon\Carbon::parse($order->order_date)->format('d M Y, h:i A') : 'N/A' }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="info-grid">
                                                    <div class="info-item">
                                                        <span class="info-label">Quantity:</span>
                                                        <span class="info-value quantity">
                                                            {{ number_format($order->quantity ?? 0) }}
                                                            <small>{{ ucfirst($order->packaging_type ?? $unit) }}</small>
                                                        </span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Rate:</span>
                                                        <span class="info-value">₹{{ number_format($order->selling_price ?? 0, 2) }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Total:</span>
                                                        <span class="info-value amount">₹{{ number_format($order->total_amount ?? 0, 2) }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="info-grid">
                                                    <div class="info-item">
                                                        <span class="info-label">Payment Mode:</span>
                                                        <span class="payment-badge {{ strtolower($order->payment_mode ?? 'online') }}">
                                                            <i class="bi {{ $order->payment_mode == 'cod' ? 'bi-cash' : 'bi-laptop' }}"></i>
                                                            {{ $order->payment_mode == 'cod' ? 'COD' : ucfirst($order->payment_mode ?? 'Online') }}
                                                        </span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Batch Used:</span>
                                                        <span class="info-value batch-ref">{{ $batch->batch_code ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="empty-history">
                                        <i class="bi bi-inbox"></i>
                                        <p>No purchase history found for this batch</p>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </td>
                </tr>
                @endif
                
                @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <div>
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
        <div class="pagination-wrap">
            <div class="pagination-info">
                Showing {{ $stocks->firstItem() ?? 0 }} to {{ $stocks->lastItem() ?? 0 }} of {{ $stocks->total() }}
            </div>
            {{ $stocks->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<style>
/* Modern CSS Variables */
:root {
    --primary: #3b82f6;
    --primary-dark: #2563eb;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --purple: #8b5cf6;
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
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.stock-page {
    max-width: 1600px;
    margin: 0 auto;
    padding: 1.5rem;
}

/* Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 1.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--gray-100);
}

.page-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--gray-800);
    margin: 0 0 0.25rem 0;
}

.page-subtitle {
    color: var(--gray-500);
    font-size: 0.875rem;
}

.page-stats {
    background: var(--gray-50);
    padding: 0.5rem 1rem;
    border-radius: 8px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--gray-600);
}

/* Workflow Steps */
.workflow-steps {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.75rem;
    padding: 1rem;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 12px;
}

.step {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem;
}

.step-number {
    width: 32px;
    height: 32px;
    background: var(--gray-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
    color: var(--gray-600);
}

.step-title {
    font-weight: 500;
    font-size: 0.875rem;
    color: var(--gray-800);
}

.step-desc {
    font-size: 0.75rem;
    color: var(--gray-400);
}

/* Filter Section */
.filter-section {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.filter-form {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    align-items: center;
}

.filter-search {
    flex: 2;
    position: relative;
}

.filter-search i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
    font-size: 0.875rem;
}

.filter-search input {
    width: 100%;
    padding: 0.5rem 0.75rem 0.5rem 2rem;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.filter-search input:focus {
    outline: none;
    border-color: var(--primary);
}

.filter-date {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    background: white;
}

.filter-date i {
    color: var(--gray-400);
    font-size: 0.875rem;
}

.filter-date input {
    border: none;
    padding: 0;
    font-size: 0.875rem;
    width: 110px;
}

.filter-date input:focus {
    outline: none;
}

.filter-date span {
    color: var(--gray-400);
}

.filter-btn {
    padding: 0.5rem 1.25rem;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
}

.filter-btn:hover {
    background: var(--primary-dark);
}

.clear-btn {
    padding: 0.5rem 1rem;
    background: var(--gray-100);
    color: var(--gray-600);
    text-decoration: none;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.clear-btn:hover {
    background: var(--gray-200);
}

/* Table Container */
.table-container {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    overflow: hidden;
}

.stock-table {
    width: 100%;
    border-collapse: collapse;
}

.stock-table th {
    text-align: left;
    padding: 1rem;
    background: var(--gray-50);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--gray-500);
    border-bottom: 1px solid var(--gray-200);
}

.stock-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--gray-100);
    font-size: 0.875rem;
}

.text-right {
    text-align: right;
}

.text-center {
    text-align: center;
}

/* Product Cell */
.product-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.product-img {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    object-fit: cover;
}

.product-img-placeholder {
    width: 40px;
    height: 40px;
    background: var(--gray-100);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-400);
}

.product-name {
    font-weight: 500;
    color: var(--gray-800);
    text-decoration: none;
    font-size: 0.875rem;
}

.product-name:hover {
    color: var(--primary);
}

.product-code {
    font-size: 0.7rem;
    color: var(--gray-400);
    margin-top: 0.15rem;
}

/* Stats Badges */
.stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 500;
}

.stat-in {
    background: rgba(59, 130, 246, 0.1);
    color: var(--primary);
}

.stat-out {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}
.stat-return {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.stat-online {
    font-size: 0.7rem;
    color: var(--success);
    margin-top: 0.2rem;
}

/* Stock Cell */
.stock-cell {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
}

.stock-value {
    display: flex;
    align-items: baseline;
    gap: 0.25rem;
}

.stock-number {
    font-size: 1rem;
    font-weight: 600;
}

.stock-number.stock-high { color: var(--success); }
.stock-number.stock-medium { color: var(--warning); }
.stock-number.stock-low { color: var(--danger); }
.stock-number.stock-out { color: var(--gray-400); }

.stock-unit {
    font-size: 0.7rem;
    color: var(--gray-400);
}

.stock-loose {
    font-size: 0.7rem;
    color: var(--gray-500);
}

.stock-bar {
    width: 80px;
    height: 3px;
    background: var(--gray-200);
    border-radius: 2px;
    overflow: hidden;
}

.stock-bar-fill {
    height: 100%;
    transition: width 0.3s;
}

.stock-bar-fill.stock-high { background: var(--success); }
.stock-bar-fill.stock-medium { background: var(--warning); }
.stock-bar-fill.stock-low { background: var(--danger); }
.stock-bar-fill.stock-out { background: var(--gray-400); }

/* Batch Badge */
.batch-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.6rem;
    background: var(--gray-100);
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--gray-600);
}

/* Action Link */
.action-link {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    background: var(--gray-100);
    color: var(--gray-600);
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    transition: all 0.2s;
}

.action-link:hover {
    background: var(--primary);
    color: white;
}

/* Expand Button */
.expand-btn {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    border: 1px solid var(--gray-200);
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.expand-btn:hover {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

.expand-btn i {
    font-size: 0.8rem;
    transition: transform 0.2s;
}

/* Batch Row */
.child-row {
    background: var(--gray-50);
}

.batch-cell {
    padding: 0 !important;
}

.batch-wrapper {
    padding: 1rem;
    border-top: 1px solid var(--gray-200);
}

.batch-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    font-weight: 500;
    font-size: 0.8rem;
    color: var(--gray-600);
}

.batch-count {
    background: var(--gray-200);
    padding: 0.15rem 0.5rem;
    border-radius: 12px;
    font-size: 0.7rem;
}

.batch-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.batch-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 0.6rem 0.75rem;
    background: white;
    border-radius: 8px;
    border: 1px solid var(--gray-200);
}

.batch-code {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-weight: 500;
    font-size: 0.8rem;
    color: var(--gray-700);
    min-width: 120px;
}

.batch-stats {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.batch-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.15rem;
}

.stat-label {
    font-size: 0.6rem;
    color: var(--gray-400);
    text-transform: uppercase;
}

.batch-stat strong {
    font-size: 0.85rem;
    font-weight: 600;
}

.batch-stat small {
    font-size: 0.6rem;
    color: var(--gray-400);
}

.batch-expiry .expiry-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.6rem;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 500;
}

.expiry-badge.valid {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.expiry-badge.warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.expiry-badge.expired {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

/* Customer Purchase History Section */
.customer-history-section {
    margin-top: 1rem;
    background: linear-gradient(135deg, var(--gray-50) 0%, white 100%);
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid var(--gray-200);
}

.history-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--gray-200);
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--gray-700);
}

.history-header i {
    color: var(--primary);
    font-size: 1rem;
}

.history-count {
    background: var(--primary);
    color: white;
    padding: 0.15rem 0.5rem;
    border-radius: 20px;
    font-size: 0.65rem;
    font-weight: 500;
    margin-left: auto;
}

/* Customer Cards Grid */
.customer-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 1rem;
}

/* Customer Card */
.customer-card {
    background: white;
    border-radius: 12px;
    border: 1px solid var(--gray-200);
    overflow: hidden;
    transition: all 0.2s ease;
}

.customer-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px -8px rgba(0, 0, 0, 0.1);
    border-color: var(--gray-300);
}

.customer-card.offline {
    border-left: 3px solid var(--primary);
}

.customer-card.online {
    border-left: 3px solid var(--purple);
}

.customer-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}

.customer-avatar {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.customer-avatar.offline-avatar {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
}

.customer-avatar.online-avatar {
    background: linear-gradient(135deg, var(--purple) 0%, #7c3aed 100%);
    color: white;
}

.customer-info {
    flex: 1;
}

.customer-name {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--gray-800);
    margin-bottom: 0.2rem;
}

.customer-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.type-badge {
    font-size: 0.65rem;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    font-weight: 500;
}

.type-badge.offline {
    background: rgba(59, 130, 246, 0.1);
    color: var(--primary);
}

.type-badge.online {
    background: rgba(139, 92, 246, 0.1);
    color: var(--purple);
}

.doctor-badge,
.delivery-badge {
    font-size: 0.65rem;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    background: var(--gray-200);
    color: var(--gray-600);
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
}

.order-status {
    flex-shrink: 0;
}

.status-badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.6rem;
    border-radius: 20px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

.status-badge.completed,
.status-badge.delivered {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.status-badge.pending {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.customer-card-body {
    padding: 1rem;
}

.info-grid {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.75rem;
    flex-wrap: wrap;
}

.info-item {
    flex: 1;
    min-width: 120px;
}

.info-label {
    font-size: 0.65rem;
    color: var(--gray-400);
    text-transform: uppercase;
    display: block;
    margin-bottom: 0.2rem;
}

.info-value {
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--gray-700);
}

.info-value.highlight {
    font-family: monospace;
    font-weight: 600;
    color: var(--primary);
}

.info-value.quantity {
    font-weight: 600;
}

.info-value.quantity small {
    font-weight: 400;
    font-size: 0.7rem;
    color: var(--gray-500);
}

.info-value.amount {
    font-weight: 700;
    color: var(--success);
    font-size: 0.9rem;
}

.info-value.batch-ref {
    font-family: monospace;
    font-size: 0.75rem;
}

.payment-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.75rem;
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
    background: var(--gray-100);
    color: var(--gray-700);
}

.payment-badge.cash,
.payment-badge.cod {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.payment-badge.upi,
.payment-badge.online {
    background: rgba(59, 130, 246, 0.1);
    color: var(--primary);
}

.payment-badge.card {
    background: rgba(139, 92, 246, 0.1);
    color: var(--purple);
}

/* Empty History */
.empty-history {
    text-align: center;
    padding: 2rem;
    color: var(--gray-400);
}

.empty-history i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    display: block;
}

.empty-history p {
    font-size: 0.8rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem;
}

.empty-state i {
    font-size: 3rem;
    color: var(--gray-300);
    margin-bottom: 1rem;
}

.empty-state h5 {
    margin: 0 0 0.25rem 0;
    color: var(--gray-600);
}

.empty-state p {
    color: var(--gray-400);
    font-size: 0.875rem;
}

/* Pagination */
.pagination-wrap {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-top: 1px solid var(--gray-200);
    background: var(--gray-50);
}

.pagination-info {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.pagination-wrap .pagination {
    margin: 0;
}

.pagination-wrap .page-link {
    border: none;
    margin: 0 0.15rem;
    border-radius: 6px;
    padding: 0.35rem 0.7rem;
    color: var(--gray-600);
    font-size: 0.8rem;
}

.pagination-wrap .page-item.active .page-link {
    background: var(--primary);
    color: white;
}

/* Parent Row Hover */
.parent-row {
    cursor: pointer;
    transition: background 0.2s;
}

.parent-row:hover {
    background: var(--gray-50);
}

/* Responsive */
@media (max-width: 1024px) {
    .customer-cards-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .stock-page {
        padding: 1rem;
    }
    
    .workflow-steps {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .filter-form {
        flex-direction: column;
    }
    
    .filter-search,
    .filter-date {
        width: 100%;
    }
    
    .filter-date input {
        width: auto;
        flex: 1;
    }
    
    .stock-table th:nth-child(3),
    .stock-table td:nth-child(3),
    .stock-table th:nth-child(4),
    .stock-table td:nth-child(4) {
        display: none;
    }
    
    .batch-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .batch-stats {
        width: 100%;
        justify-content: space-between;
    }
    
    .pagination-wrap {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .info-grid {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .customer-card-header {
        flex-wrap: wrap;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Expand/Collapse functionality
    document.querySelectorAll('.expand-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const id = this.dataset.id;
            const row = document.getElementById(`batch-row-${id}`);
            const icon = this.querySelector('i');
            
            if (row && row.style.display === 'none') {
                row.style.display = 'table-row';
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-down');
            } else if (row) {
                row.style.display = 'none';
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-right');
            }
        });
    });
    
    // Row click to expand
    document.querySelectorAll('.parent-row').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('.expand-btn') || e.target.closest('.action-link')) {
                return;
            }
            const expandBtn = this.querySelector('.expand-btn');
            if (expandBtn) {
                expandBtn.click();
            }
        });
    });
});
</script>

@if(!isset($hasBootstrapIcons))
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endif

@endsection