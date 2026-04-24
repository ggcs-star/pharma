@extends('layouts.master')

@section('title', 'Purchase Orders')

@section('content')

<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="bg-primary bg-opacity-10 p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="fas fa-shopping-cart text-primary fa-lg"></i>
                </div>
                <div>
                    <h1 class="h3 mb-0 fw-bold">Purchase Orders</h1>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-2">Manage all purchase orders and track status</p>
        </div>
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-gradient-primary px-4 py-2 shadow-sm">
            <i class="fas fa-plus-circle me-2"></i>Create Purchase Order
        </a>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-card-inner">
                    <div>
                        <p class="stat-label">Total Orders</p>
                        <h2 class="stat-value">{{ $orders->total() }}</h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar" style="width: 100%;"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-card-inner">
                    <div>
                        <p class="stat-label">Pending Orders</p>
                        <h2 class="stat-value">{{ $orders->where('status', 'pending')->count() }}</h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar" style="width: {{ $orders->total() > 0 ? ($orders->where('status', 'pending')->count() / $orders->total() * 100) : 0 }}%;"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-success">
                <div class="stat-card-inner">
                    <div>
                        <p class="stat-label">Delivered</p>
                        <h2 class="stat-value">{{ $orders->where('status', 'delivered')->count() }}</h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar" style="width: {{ $orders->total() > 0 ? ($orders->where('status', 'delivered')->count() / $orders->total() * 100) : 0 }}%;"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-danger">
                <div class="stat-card-inner">
                    <div>
                        <p class="stat-label">Cancelled</p>
                        <h2 class="stat-value">{{ $orders->where('status', 'cancelled')->count() }}</h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar" style="width: {{ $orders->total() > 0 ? ($orders->where('status', 'cancelled')->count() / $orders->total() * 100) : 0 }}%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card filter-card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('purchase-orders.index') }}" id="filterForm">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3 col-lg-3">
                        <label class="form-label small text-muted mb-1">
                            <i class="fas fa-search me-1"></i>Search
                        </label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search fa-xs text-muted"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control form-control-sm border-start-0 ps-0" 
                                   placeholder="Order number or supplier..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="form-label small text-muted mb-1">
                            <i class="fas fa-calendar-alt me-1"></i>From
                        </label>
                        <input type="date" 
                               name="from_date" 
                               class="form-control form-control-sm" 
                               value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="form-label small text-muted mb-1">
                            <i class="fas fa-calendar-alt me-1"></i>To
                        </label>
                        <input type="date" 
                               name="to_date" 
                               class="form-control form-control-sm" 
                               value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="form-label small text-muted mb-1">
                            <i class="fas fa-tag me-1"></i>Status
                        </label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>✓ Confirmed</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>⚙ Processing</option>
                            <option value="dispatched" {{ request('status') == 'dispatched' ? 'selected' : '' }}>🚚 Dispatched</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>✅ Delivered</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="form-label small text-muted mb-1">
                            <i class="fas fa-list-ul me-1"></i>Per Page
                        </label>
                        <select name="per_page" class="form-select form-select-sm">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 entries</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 entries</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 entries</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 entries</option>
                        </select>
                    </div>
                    <div class="col-md-1 col-lg-1">
                        <div class="d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-primary w-100" title="Apply filters">
                                <i class="fas fa-filter"></i>
                            </button>
                            <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset filters">
                                <i class="fas fa-undo-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card table-card">
        <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0 px-0">
            <div class="d-flex justify-content-between align-items-center px-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="fas fa-list text-primary me-2"></i>Purchase Order List
                </h6>
                <span class="badge bg-light text-dark">
                    <i class="fas fa-file-alt me-1"></i>{{ $orders->total() }} Orders
                </span>
            </div>
            <hr class="my-2 mx-3">
        </div>
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Order No</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th class="text-center">Items</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusConfig = [
                            'pending' => ['color' => '#f59e0b', 'bg' => '#fffbeb', 'icon' => 'fa-clock'],
                            'confirmed' => ['color' => '#3b82f6', 'bg' => '#eff6ff', 'icon' => 'fa-check-circle'],
                            'processing' => ['color' => '#06b6d4', 'bg' => '#ecfeff', 'icon' => 'fa-cog'],
                            'dispatched' => ['color' => '#8b5cf6', 'bg' => '#f5f3ff', 'icon' => 'fa-truck'],
                            'delivered' => ['color' => '#10b981', 'bg' => '#ecfdf5', 'icon' => 'fa-check-double'],
                            'cancelled' => ['color' => '#ef4444', 'bg' => '#fef2f2', 'icon' => 'fa-times-circle'],
                            'rejected' => ['color' => '#6b7280', 'bg' => '#f3f4f6', 'icon' => 'fa-ban'],
                        ];
                    @endphp

                    @forelse($orders as $order)
                    <tr class="order-row" data-order-id="{{ $order->id }}">
                        <td class="ps-3">
                            <span class="text-muted small">#{{ $order->id }}</span>
                        </td>
                        <td>
                            <span class="order-number">{{ $order->order_number }}</span>
                        </td>
                        <td>
                            <div class="fw-medium">{{ \Carbon\Carbon::parse($order->order_date)->format('d M, Y') }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($order->order_date)->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="supplier-avatar">
                                    {{ substr($order->supplier->name ?? 'N/A', 0, 2) }}
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $order->supplier->name ?? 'N/A' }}</div>
                                    <small class="text-muted">Code: {{ $order->supplier->supplier_code ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="items-badge">
                                <i class="fas fa-boxes me-1"></i>{{ $order->items_count ?? 0 }}
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="amount">₹ {{ number_format($order->total_amount ?? 0, 2) }}</span>
                        </td>
                        <td class="text-center">
                            @php $cfg = $statusConfig[$order->status] ?? $statusConfig['pending']; @endphp
                            <span class="status-badge" style="background: {{ $cfg['bg'] }}; color: {{ $cfg['color'] }}; border-left-color: {{ $cfg['color'] }};">
                                <i class="fas {{ $cfg['icon'] }} me-1"></i>
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="text-center pe-3">
                            <div class="action-buttons">
                                {{-- View Button - Always Visible --}}
                                <a href="{{ route('purchase-orders.show', $order->id) }}" 
                                   class="action-btn view-btn" 
                                   data-bs-toggle="tooltip" 
                                   title="View Order">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- Cancel Button - Only before dispatched (Pending, Confirmed, Processing) --}}
                                @if(
                                    !$order->published_for_sale && 
                                    !in_array($order->status, ['dispatched', 'delivered', 'cancelled', 'rejected'])
                                )
                                    <button type="button" 
                                            class="action-btn cancel-btn" 
                                            onclick="cancelOrder({{ $order->id }})"
                                            data-bs-toggle="tooltip" 
                                            title="Cancel Order">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <form id="cancel-form-{{ $order->id }}" 
                                          method="POST" 
                                          action="{{ url('/purchase-orders/'.$order->id.'/status') }}" 
                                          style="display: none;">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                    </form>
                                @endif

                                {{-- 1. Receive Stock Button - Only when status is dispatched AND stock not received --}}
                                @if($order->status == 'dispatched' && !$order->stock_received)
                                    <form method="POST" action="{{ url('/po/'.$order->id.'/receive-stock') }}" class="d-inline">
                                        @csrf
                                        <button type="submit"
                                                class="action-btn receive-btn"
                                                data-bs-toggle="tooltip"
                                                title="Receive Stock">
                                            <i class="fas fa-dolly-flatbed"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- 2. Update MRP Button - Only after stock received AND price not updated yet --}}
                                @if($order->status == 'dispatched' && $order->stock_received && !$order->price_updated)
                                    <button type="button"
                                            class="action-btn price-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#updatePriceModal{{ $order->id }}"
                                            title="Update Selling Price">
                                        <i class="fas fa-tag"></i>
                                    </button>
                                @endif

                                {{-- 3. Publish for Sale Button OR Published Badge - IMPROVED LOGIC --}}
                            @if(
    $order->status == 'dispatched' &&
    $order->stock_received &&
    $order->price_updated
)

    {{-- Only one time publish allowed --}}

    @if((int) $order->published_for_sale === 0)

        {{-- First time only → Show Publish Button --}}

        <form method="POST"
              action="{{ url('/po/'.$order->id.'/publish-sale') }}"
              class="d-inline publish-form"
              data-order-id="{{ $order->id }}">

            @csrf

            <button type="submit"
                    class="action-btn publish-btn"
                    data-bs-toggle="tooltip"
                    title="Publish for Sale">

                <i class="fas fa-store"></i>

            </button>
        </form>

    @else

        {{-- After publish → Button removed forever --}}

        <span class="published-badge"
              data-bs-toggle="tooltip"
              title="Already Published for Sale">

            <i class="fas fa-check-circle me-1"></i>
            Published

        </span>

    @endif

@endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <h5 class="mt-3 mb-2">No Purchase Orders Found</h5>
                                <p class="text-muted small mb-3">Get started by creating your first purchase order</p>
                                <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-2"></i>Create Purchase Order
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->isNotEmpty())
        <div class="card-footer bg-transparent border-top-0 pt-0 pb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="text-muted small">
                    <i class="fas fa-database me-1"></i>
                    Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} 
                    of {{ $orders->total() }} entries
                </div>
                <div>
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

{{-- ALL MODALS PLACED OUTSIDE TABLE FOR PROPER BOOTSTRAP RENDERING --}}
@if($orders->isNotEmpty())
    @foreach($orders as $order)
    <!-- Update Price Modal for Order #{{ $order->id }} -->
    <div class="modal fade" id="updatePriceModal{{ $order->id }}" tabindex="-1" aria-labelledby="updatePriceModalLabel{{ $order->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form method="POST" action="{{ url('/po/'.$order->id.'/update-mrp') }}">
                    @csrf
                    <div class="modal-header border-0 pt-4 px-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                <i class="fas fa-tag text-primary fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0" id="updatePriceModalLabel{{ $order->id }}">Update Selling Price</h5>
                                <p class="text-muted small mb-0 mt-1">Set final MRP for all products in this order</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body px-4 py-3">
                        <!-- Order Info Card -->
                        <div class="bg-light rounded-3 p-3 mb-4">
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Order Number</small>
                                    <span class="fw-semibold">{{ $order->order_number }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Supplier</small>
                                    <span class="fw-semibold">{{ $order->supplier->name ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Total Items</small>
                                    <span class="fw-semibold">{{ $order->items_count ?? 0 }} products</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Purchase Total</small>
                                    <span class="fw-semibold text-success">₹ {{ number_format($order->total_amount ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- MRP Input - FIXED -->
                        <div class="mb-3">
    <label class="form-label fw-semibold mb-2">
        <i class="fas fa-rupee-sign text-primary me-1"></i>
        Final MRP (₹)
    </label>

    <div class="input-group input-group-lg">
        <span class="input-group-text bg-light border-end-0">
            <i class="fas fa-rupee-sign text-muted"></i>
        </span>

        <input type="number"
               step="0.01"
               name="final_mrp"
               class="form-control border-start-0"
               placeholder="Enter Final MRP"
               value=""
               autocomplete="off"
               min="0.01"
               required>
    </div>

    <div class="form-text text-muted mt-2">
        <i class="fas fa-info-circle me-1"></i>
        Final selling price for this PO
    </div>
</div>

{{-- NEW → Conversion Factor Input --}}
<div class="mb-3">
    <label class="form-label fw-semibold mb-2">
        <i class="fas fa-capsules text-primary me-1"></i>
        Strip Size / Conversion Factor
    </label>

    <div class="input-group input-group-lg">
        <span class="input-group-text bg-light border-end-0">
            <i class="fas fa-layer-group text-muted"></i>
        </span>

        <input type="number"
               step="1"
               name="conversion_factor"
               class="form-control border-start-0"
               placeholder="Example: 10"
               value="10"
               autocomplete="off"
               min="1"
               required>
    </div>

    <div class="form-text text-muted mt-2">
        <i class="fas fa-info-circle me-1"></i>
        Example:
        1 strip = 10 tablets → enter 10<br>
        1 strip = 9 tablets → enter 9<br>
        1 strip = 15 tablets → enter 15
    </div>
</div>
                        
                        <!-- Price Preview - FIXED DEFAULT TEXT -->
                        <div class="alert alert-info bg-info bg-opacity-10 border-0 rounded-3 mt-3">
                            <div class="d-flex gap-2">
                                <i class="fas fa-calculator text-info mt-1"></i>
                                <div>
                                    <small class="d-block text-muted">Estimated Profit Margin</small>
                                    <span class="fw-semibold profit-preview-text" data-profit-preview="{{ $order->id }}">Enter Final MRP to see profit preview</span>
                                    <small class="text-muted d-block mt-1">
                                        Based on purchase total: ₹ {{ number_format($order->total_amount ?? 0, 2) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>Save MRP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endif

@push('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        --shadow-sm: 0 2px 4px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.03);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -2px rgba(0,0,0,0.025);
        --transition-default: all 0.2s ease;
    }
    
    .btn-gradient-primary {
        background: var(--primary-gradient);
        border: none;
        color: white;
        transition: var(--transition-default);
    }
    
    .btn-gradient-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        color: white;
    }
    
    .stat-card {
        background: white;
        border-radius: 1rem;
        padding: 1.25rem;
        box-shadow: var(--shadow-md);
        transition: var(--transition-default);
        border: 1px solid rgba(0,0,0,0.03);
        position: relative;
        overflow: hidden;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }
    
    .stat-card-inner {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    
    .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .stat-progress {
        margin-top: 1rem;
        height: 3px;
        background: rgba(0,0,0,0.05);
        border-radius: 10px;
        overflow: hidden;
    }
    
    .stat-progress .progress-bar {
        height: 100%;
        border-radius: 10px;
        transition: width 0.3s ease;
    }
    
    .stat-card-primary .stat-icon { background: rgba(13, 110, 253, 0.1); color: #0d6efd; }
    .stat-card-primary .stat-value { color: #0d6efd; }
    .stat-card-primary .progress-bar { background: linear-gradient(90deg, #0d6efd, #0a58ca); }
    
    .stat-card-warning .stat-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .stat-card-warning .stat-value { color: #f59e0b; }
    .stat-card-warning .progress-bar { background: linear-gradient(90deg, #f59e0b, #d97706); }
    
    .stat-card-success .stat-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .stat-card-success .stat-value { color: #10b981; }
    .stat-card-success .progress-bar { background: linear-gradient(90deg, #10b981, #059669); }
    
    .stat-card-danger .stat-icon { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .stat-card-danger .stat-value { color: #ef4444; }
    .stat-card-danger .progress-bar { background: linear-gradient(90deg, #ef4444, #dc2626); }
    
    .filter-card {
        border: none;
        box-shadow: var(--shadow-sm);
        background: white;
        border-radius: 0.75rem;
    }
    
    .filter-card .form-control-sm,
    .filter-card .form-select-sm {
        border-radius: 0.5rem;
        background-color: #f8fafc;
        border-color: #e2e8f0;
    }
    
    .table-card {
        border: none;
        border-radius: 1rem;
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }
    
    .table-modern {
        font-size: 0.85rem;
    }
    
    .table-modern thead th {
        background: #f8fafc;
        padding: 1rem 0.75rem;
        font-weight: 600;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .table-modern tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .order-row {
        transition: var(--transition-default);
    }
    
    .order-row:hover {
        background-color: #fafcff;
    }
    
    .order-number {
        font-weight: 600;
        color: #0d6efd;
        font-family: 'Monaco', 'Menlo', monospace;
        font-size: 0.8rem;
    }
    
    .supplier-avatar {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #f0f4ff 0%, #e8eeff 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.75rem;
        color: #0d6efd;
        text-transform: uppercase;
    }
    
    .items-badge {
        background: #f1f5f9;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .amount {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.9rem;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.3rem 0.8rem;
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 600;
        border-left: 3px solid;
        letter-spacing: 0.3px;
    }
    
    .action-buttons {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
        transition: var(--transition-default);
        cursor: pointer;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
    }
    
    .view-btn:hover {
        background: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
    
    .receive-btn:hover {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }
    
    .price-btn:hover {
        background: #f59e0b;
        border-color: #f59e0b;
        color: white;
    }
    
    .publish-btn:hover {
        background: #8b5cf6;
        border-color: #8b5cf6;
        color: white;
    }
    
    .cancel-btn:hover {
        background: #ef4444;
        border-color: #ef4444;
        color: white;
    }
    
    /* Enterprise Published Badge */
    .published-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        font-size: 0.7rem;
        font-weight: 600;
        border-radius: 30px;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
        white-space: nowrap;
        transition: var(--transition-default);
        border: none;
        cursor: default;
    }
    
    .published-badge i {
        font-size: 0.7rem;
    }
    
    .published-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.25);
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }
    
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
    }
    
    .empty-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #f8fafe 0%, #f0f4ff 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 2.5rem;
        color: #0d6efd;
    }
    
    .pagination {
        gap: 4px;
        margin: 0;
    }
    
    .page-link {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        color: #475569;
        background: white;
        transition: var(--transition-default);
    }
    
    .page-link:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #0d6efd;
    }
    
    .page-item.active .page-link {
        background: var(--primary-gradient);
        border-color: #0d6efd;
        color: white;
    }
    
    .page-item.disabled .page-link {
        background: #f8fafc;
        color: #94a3b8;
    }
    
    /* Modal Styles */
    .modal-content {
        border-radius: 1rem;
    }
    
    .modal-header .btn-close {
        background-color: #f1f5f9;
        border-radius: 50%;
        padding: 0.5rem;
        opacity: 1;
    }
    
    .modal-header .btn-close:hover {
        background-color: #e2e8f0;
    }
    
    /* Alert Styles */
    .alert {
        border: none;
    }
    
    @media (max-width: 768px) {
        .stat-value {
            font-size: 1.5rem;
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
        }
        
        .action-btn {
            width: 28px;
            height: 28px;
            font-size: 0.7rem;
        }
        
        .published-badge {
            padding: 0.2rem 0.6rem;
            font-size: 0.65rem;
        }
        
        .published-badge i {
            font-size: 0.65rem;
        }
        
        .table-modern tbody td {
            padding: 0.75rem 0.5rem;
        }
        
        .order-number {
            font-size: 0.7rem;
        }
        
        .amount {
            font-size: 0.8rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Auto-submit filters
        document.querySelector('select[name="per_page"]')?.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
        
        document.querySelector('select[name="status"]')?.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
        
        // Search with debounce
        let searchTimeout;
        let searchInput = document.querySelector('input[name="search"]');
        if(searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            });
        }
        
        // Date inputs auto-submit
        document.querySelectorAll('input[type="date"]').forEach(input => {
            input.addEventListener('change', () => {
                document.getElementById('filterForm').submit();
            });
        });
        
        // Handle Publish Form Submission with SweetAlert confirmation
        document.querySelectorAll('.publish-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const orderId = this.dataset.orderId;
                const formElement = this;
                
                Swal.fire({
                    title: 'Publish for Sale?',
                    text: "This will make all products available for sale in the inventory.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#8b5cf6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, publish',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-4',
                        confirmButton: 'btn btn-primary px-4',
                        cancelButton: 'btn btn-light px-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Publishing products for sale',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                                formElement.submit();
                            }
                        });
                    }
                });
            });
        });
    });
function updateProfitPreview(orderId, totalAmount) {
    const modal = document.getElementById(`updatePriceModal${orderId}`);
    if (!modal) return;

    const priceInput = modal.querySelector('input[name="final_mrp"]');
    const profitPreviewSpan = modal.querySelector('.profit-preview-text');

    if (!priceInput || !profitPreviewSpan) return;

    priceInput.addEventListener('input', function () {
        const mrp = parseFloat(this.value);

        if (!mrp || mrp <= 0) {
            profitPreviewSpan.innerHTML = `
                <span class="text-muted">
                    Enter Final MRP to see profit preview
                </span>
            `;
            return;
        }

        const totalProfit = mrp - totalAmount;

        const marginPercent = totalAmount > 0
            ? ((totalProfit / totalAmount) * 100)
            : 0;

        profitPreviewSpan.innerHTML = `
            <span class="${totalProfit >= 0 ? 'text-success' : 'text-danger'} fw-semibold">
                ₹ ${totalProfit.toFixed(2)} profit
            </span>
            <small class="d-block text-muted">
                ${marginPercent.toFixed(1)}% margin
            </small>
        `;
    });
}

    // Initialize profit preview for each order modal
 @foreach($orders as $order)
updateProfitPreview(
    {{ $order->id }},
    {{ $order->total_amount ?? 0 }}
);
@endforeach
    
    // Cancel order function
    function cancelOrder(id) {
        Swal.fire({
            title: 'Cancel Purchase Order?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, cancel order',
            cancelButtonText: 'No, keep it',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-4',
                confirmButton: 'btn btn-danger px-4',
                cancelButton: 'btn btn-light px-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('cancel-form-' + id);
                if (form) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Cancelling your order',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            form.submit();
                        }
                    });
                }
            }
        });
    }
</script>
@endpush