@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section with Stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-box-seam me-2 text-primary"></i>Orders Management
                    </h3>
                    <p class="text-muted mb-0">Manage and track all customer orders</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" id="exportBtn" onclick="exportOrders()">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
                    <button class="btn btn-outline-info" id="refreshBtn" onclick="refreshPage()">
                        <i class="bi bi-arrow-repeat me-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Orders</h6>
                            <h3 class="fw-bold mb-0">{{ $orders->total() }}</h3>
                        </div>
                        <i class="bi bi-cart fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-success text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Revenue</h6>
                            <h3 class="fw-bold mb-0">
                                ₹{{ number_format($orders->sum('total'), 0) }}
                            </h3>
                        </div>
                        <i class="bi bi-currency-rupee fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Pending Orders</h6>
                            <h3 class="fw-bold mb-0">
                                {{ $orders->where('status', 'pending')->count() }}
                            </h3>
                        </div>
                        <i class="bi bi-clock-history fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-info text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">With Prescription</h6>
                            <h3 class="fw-bold mb-0">
                                {{ $orders->whereNotNull('prescription_id')->count() }}
                            </h3>
                        </div>
                        <i class="bi bi-file-medical fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white border-bottom-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-funnel me-2"></i>Filter Orders
                </h5>
                <span class="badge bg-light text-dark">
                    <i class="bi bi-info-circle"></i> {{ $orders->total() }} records found
                </span>
            </div>
        </div>
        <div class="card-body">
            <!-- Enhanced Filter Form -->
            <form method="GET" class="row g-3 mb-4 p-3 bg-light rounded-3">
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">
                        <i class="bi bi-calendar3"></i> From Date
                    </label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" 
                           class="form-control form-control-sm border-0 shadow-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">
                        <i class="bi bi-calendar3"></i> To Date
                    </label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" 
                           class="form-control form-control-sm border-0 shadow-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">
                        <i class="bi bi-tag"></i> Status
                    </label>
                    <select name="status" class="form-select form-select-sm border-0 shadow-sm">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status')=='pending'?'selected':'' }}>⏳ Pending</option>
                        <option value="confirmed" {{ request('status')=='confirmed'?'selected':'' }}>✅ Confirmed</option>
                        <option value="processing" {{ request('status')=='processing'?'selected':'' }}>⚙️ Processing</option>
                        <option value="shipped" {{ request('status')=='shipped'?'selected':'' }}>📦 Shipped</option>
                        <option value="delivered" {{ request('status')=='delivered'?'selected':'' }}>🏠 Delivered</option>
                        <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>❌ Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">
                        <i class="bi bi-prescription2"></i> Prescription
                    </label>
                    <select name="rx" class="form-select form-select-sm border-0 shadow-sm">
                        <option value="">All Rx</option>
                        <option value="yes" {{ request('rx')=='yes'?'selected':'' }}>✔ With Rx</option>
                        <option value="no" {{ request('rx')=='no'?'selected':'' }}>✖ Without Rx</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">
                        <i class="bi bi-search"></i> Quick Search
                    </label>
                    <input type="text" name="search" placeholder="Order ID / Customer" 
                           value="{{ request('search') }}" class="form-control form-control-sm border-0 shadow-sm">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button class="btn btn-primary btn-sm flex-grow-1 shadow-sm">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm flex-grow-1 shadow-sm">
                        <i class="bi bi-arrow-repeat me-1"></i>Reset
                    </a>
                </div>
            </form>

            <!-- Orders Table - Responsive -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-uppercase small fw-semibold">
                            <th>#ID</th>
                            <th>Customer</th>
                            <th>Order Total</th>
                            <th>Payment</th>
                            <th>Prescription</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr class="border-bottom">
                            <td class="fw-bold">
                                <span class="badge bg-light text-dark">#{{ $order->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 35px; height: 35px; font-size: 14px;">
                                        {{ strtoupper(substr($order->user->name ?? 'G', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $order->user->name ?? 'Guest User' }}</div>
                                        <small class="text-muted">{{ $order->user->email ?? 'No email' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-success">
                                    ₹{{ number_format($order->total, 2) }}
                                </span>
                            </td>
                            <td>
                                @if($order->payment_mode == 'razorpay')
                                    @if($order->payment_status == 'paid')
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                            <i class="bi bi-check-circle-fill me-1"></i> Paid Online
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
                                            <i class="bi bi-clock-fill me-1"></i> Pending
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">
                                        <i class="bi bi-cash-stack me-1"></i> Cash on Delivery
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($order->prescription_id)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                        <i class="bi bi-file-check me-1"></i> Uploaded
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">
                                        <i class="bi bi-file-x me-1"></i> Not Required
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusConfig = [
                                        'pending' => ['bg' => 'warning', 'icon' => 'clock', 'text' => 'Pending'],
                                        'confirmed' => ['bg' => 'info', 'icon' => 'check-circle', 'text' => 'Confirmed'],
                                        'processing' => ['bg' => 'primary', 'icon' => 'gear', 'text' => 'Processing'],
                                        'shipped' => ['bg' => 'dark', 'icon' => 'truck', 'text' => 'Shipped'],
                                        'delivered' => ['bg' => 'success', 'icon' => 'house-check', 'text' => 'Delivered'],
                                        'cancelled' => ['bg' => 'danger', 'icon' => 'x-circle', 'text' => 'Cancelled'],
                                    ];
                                    $config = $statusConfig[$order->status] ?? ['bg' => 'secondary', 'icon' => 'question', 'text' => ucfirst($order->status ?? '-')];
                                @endphp
                                <span class="badge bg-{{ $config['bg'] }} bg-opacity-10 text-{{ $config['bg'] }} px-3 py-2 rounded-pill">
                                    <i class="bi bi-{{ $config['icon'] }} me-1"></i> {{ $config['text'] }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $order->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                   class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                <h6 class="text-muted">No orders found</h6>
                                <small>Try adjusting your filters</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination with Info -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-2">
                <div class="text-muted small mb-3 mb-md-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Showing 
                    <strong>{{ $orders->firstItem() ?? 0 }}</strong> 
                    to 
                    <strong>{{ $orders->lastItem() ?? 0 }}</strong> 
                    of 
                    <strong>{{ $orders->total() }}</strong> 
                    entries
                </div>
                <div>
                    {{ $orders->appends(request()->all())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Styles */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .avatar-circle {
        width: 35px;
        height: 35px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-weight: bold;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
        transition: all 0.2s ease;
    }
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        border-color: #667eea;
    }
    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: transparent;
    }
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
</style>

<script>
    function exportOrders() {
        // Add export functionality
        let currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('export', 'csv');
        window.location.href = currentUrl.toString();
    }
    
    function refreshPage() {
        window.location.reload();
    }
    
    // Quick search with debounce
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length > 2 || this.value.length === 0) {
                    this.closest('form').submit();
                }
            }, 500);
        });
    }
</script>

@endsection