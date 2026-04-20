@extends('layouts.master')

@section('title', 'Purchase List')

@section('content')

<div class="container-fluid px-4 py-3">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-shopping-cart text-primary me-2"></i>Purchase Management
            </h1>
            <p class="text-muted small mb-0">Track and manage all your purchase transactions</p>
        </div>
        <a href="{{ route('purchase.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus-circle me-2"></i>New Purchase
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 stats-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small fw-semibold">Total Purchases</span>
                            <h2 class="mb-0 fw-bold mt-1 text-primary">{{ $totalPurchases ?? $purchases->total() }}</h2>
                        </div>
                        <div class="stats-icon bg-primary-soft">
                            <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 stats-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small fw-semibold">Total Amount</span>
                            <h2 class="mb-0 fw-bold mt-1 text-success">₹ {{ number_format($totalAmount ?? $purchases->sum('net_amount'), 2) }}</h2>
                        </div>
                        <div class="stats-icon bg-success-soft">
                            <i class="fas fa-rupee-sign fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 stats-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small fw-semibold">Total GST</span>
                            <h2 class="mb-0 fw-bold mt-1 text-info">₹ {{ number_format($totalGST ?? $purchases->sum('total_gst'), 2) }}</h2>
                        </div>
                        <div class="stats-icon bg-info-soft">
                            <i class="fas fa-percent fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 stats-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small fw-semibold">Total Discount</span>
                            <h2 class="mb-0 fw-bold mt-1 text-warning">₹ {{ number_format($totalDiscount ?? $purchases->sum('total_discount'), 2) }}</h2>
                        </div>
                        <div class="stats-icon bg-warning-soft">
                            <i class="fas fa-tag fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-sliders-h text-primary me-2"></i>
                <h6 class="mb-0 fw-semibold">Filter Options</h6>
            </div>
        </div>
        <div class="card-body pt-0 px-4 pb-4">
            <form method="GET" action="{{ route('purchase.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-2">
                            <i class="fas fa-search me-1"></i>Search Invoice
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control bg-light" 
                                   placeholder="Enter invoice number..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-2">
                            <i class="fas fa-building me-1"></i>Supplier
                        </label>
                        <select name="supplier_id" class="form-select bg-light">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->id }}" 
                                    {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-2">
                            <i class="fas fa-calendar-alt me-1"></i>From Date
                        </label>
                        <input type="date" 
                               name="from_date" 
                               class="form-control bg-light" 
                               value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-2">
                            <i class="fas fa-calendar-check me-1"></i>To Date
                        </label>
                        <input type="date" 
                               name="to_date" 
                               class="form-control bg-light" 
                               value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-2">
                            <i class="fas fa-list-ol me-1"></i>Per Page
                        </label>
                        <select name="per_page" class="form-select bg-light" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 entries</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 entries</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 entries</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 entries</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                            @if(request()->anyFilled(['search', 'supplier_id', 'from_date', 'to_date']))
                                <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                    <i class="fas fa-undo-alt me-2"></i>Clear Filters
                                </a>
                            @endif
                            <div class="ms-auto">
                                <small class="text-muted">
                                    <i class="fas fa-chart-line me-1"></i>
                                    Showing <strong>{{ $purchases->firstItem() ?? 0 }}</strong> to <strong>{{ $purchases->lastItem() ?? 0 }}</strong> 
                                    of <strong>{{ $purchases->total() }}</strong> entries
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%" class="py-3 ps-4 text-uppercase small fw-semibold text-muted">
                                <i class="fas fa-hashtag me-1"></i> ID
                            </th>
                            <th width="12%" class="py-3 text-uppercase small fw-semibold text-muted">
                                <i class="fas fa-file-invoice me-1"></i> Invoice No
                            </th>
                            <th width="20%" class="py-3 text-uppercase small fw-semibold text-muted">
                                <i class="fas fa-building me-1"></i> Supplier
                            </th>
                            <th width="12%" class="py-3 text-uppercase small fw-semibold text-muted">
                                <i class="fas fa-calendar me-1"></i> Date
                            </th>
                            <th width="10%" class="py-3 text-end text-uppercase small fw-semibold text-muted">
                                <i class="fas fa-percent me-1"></i> GST
                            </th>
                            <th width="10%" class="py-3 text-end text-uppercase small fw-semibold text-muted">
                                <i class="fas fa-tag me-1"></i> Discount
                            </th>
                            <th width="12%" class="py-3 text-end text-uppercase small fw-semibold text-muted">
                                <i class="fas fa-rupee-sign me-1"></i> Total
                            </th>
                            <th width="10%" class="py-3 text-center text-uppercase small fw-semibold text-muted">
                                <i class="fas fa-credit-card me-1"></i> Status
                            </th>
                            <th width="9%" class="py-3 text-center pe-4 text-uppercase small fw-semibold text-muted">
                                <i class="fas fa-cog me-1"></i> Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                        @php
                            $statusColor = match(strtolower($purchase->payment_type ?? 'pending')) {
                                'cash', 'paid' => 'success',
                                'pending' => 'danger',
                                'credit' => 'warning',
                                default => 'secondary'
                            };
                            $statusIcon = match(strtolower($purchase->payment_type ?? 'pending')) {
                                'cash', 'paid' => 'fa-check-circle',
                                'pending' => 'fa-clock',
                                'credit' => 'fa-credit-card',
                                default => 'fa-info-circle'
                            };
                            $statusText = match(strtolower($purchase->payment_type ?? 'pending')) {
                                'cash', 'paid' => 'Paid',
                                'pending' => 'Pending',
                                'credit' => 'Credit',
                                default => ucfirst($purchase->payment_type ?? 'Unknown')
                            };
                        @endphp
                        <tr class="purchase-row">
                            <td class="ps-4">
                                <span class="fw-semibold text-muted">#{{ $purchase->id }}</span>
                            </td>
                            <td>
                                <span class="invoice-number">
                                    <i class="fas fa-receipt me-1"></i>
                                    INV-{{ str_pad($purchase->invoice_number, 6, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="supplier-avatar">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $purchase->supplier->name ?? 'N/A' }}</div>
                                        <small class="text-muted">ID: {{ $purchase->supplier_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="purchase-date">
                                    <i class="far fa-calendar-alt text-muted me-1"></i>
                                    {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M, Y') }}
                                </div>
                                <small class="text-muted time-ago">
                                    <i class="far fa-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($purchase->created_at)->diffForHumans() }}
                                </small>
                            </td>
                            <td class="text-end">
                                <span class="gst-badge">
                                    <i class="fas fa-percent me-1"></i>
                                    ₹ {{ number_format($purchase->total_gst, 2) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="discount-badge">
                                    <i class="fas fa-tag me-1"></i>
                                    ₹ {{ number_format($purchase->total_discount, 2) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="total-badge">
                                    <i class="fas fa-rupee-sign me-1"></i>
                                    {{ number_format($purchase->net_amount, 2) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="status-badge status-{{ $statusColor }}">
                                    <i class="fas {{ $statusIcon }} me-1"></i>
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="action-buttons">
                                    <a href="{{ route('purchase.show', $purchase->id) }}" 
                                       class="btn-action btn-view" 
                                       data-bs-toggle="tooltip" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('purchase.edit', $purchase->id) }}" 
                                       class="btn-action btn-edit" 
                                       data-bs-toggle="tooltip" 
                                       title="Edit Purchase">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn-action btn-delete" 
                                            onclick="confirmDelete({{ $purchase->id }})"
                                            data-bs-toggle="tooltip" 
                                            title="Delete Purchase">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $purchase->id }}" 
                                      action="{{ route('purchase.destroy', $purchase->id) }}" 
                                      method="POST" 
                                      style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Purchases Found</h5>
                                    <p class="text-muted small mb-3">Get started by creating your first purchase</p>
                                    <a href="{{ route('purchase.create') }}" class="btn btn-primary rounded-pill">
                                        <i class="fas fa-plus me-2"></i>Create New Purchase
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($purchases->isNotEmpty())
        <div class="card-footer bg-white border-0 py-3 px-4 rounded-bottom-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="pagination-info">
                    <i class="fas fa-info-circle text-muted me-1"></i>
                    <small class="text-muted">
                        Showing <strong>{{ $purchases->firstItem() }}</strong> to <strong>{{ $purchases->lastItem() }}</strong> 
                        of <strong>{{ $purchases->total() }}</strong> entries
                    </small>
                </div>
                <div class="pagination-wrapper">
                    {{ $purchases->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Stats Cards */
    .stats-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    
    .stats-icon {
        width: 55px;
        height: 55px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bg-primary-soft { background: linear-gradient(135deg, #eef2ff, #e0e7ff); }
    .bg-success-soft { background: linear-gradient(135deg, #ecfdf5, #d1fae5); }
    .bg-info-soft { background: linear-gradient(135deg, #ecfeff, #cffafe); }
    .bg-warning-soft { background: linear-gradient(135deg, #fefce8, #fef3c7); }

    /* Supplier Avatar */
    .supplier-avatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 18px;
    }

    /* Invoice Number */
    .invoice-number {
        font-weight: 600;
        color: #4f46e5;
        background: #eef2ff;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 13px;
        display: inline-block;
    }

    /* Purchase Date */
    .purchase-date {
        font-weight: 500;
        color: #1f2937;
        font-size: 13px;
    }
    
    .time-ago {
        font-size: 11px;
    }

    /* Badges */
    .gst-badge {
        background: #ecfeff;
        color: #0891b2;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    
    .discount-badge {
        background: #fefce8;
        color: #d97706;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    
    .total-badge {
        background: #ecfdf5;
        color: #059669;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        display: inline-block;
    }

    /* Status Badges */
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .status-success {
        background: #ecfdf5;
        color: #059669;
    }
    
    .status-danger {
        background: #fef2f2;
        color: #dc2626;
    }
    
    .status-warning {
        background: #fffbeb;
        color: #d97706;
    }
    
    .status-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 5px;
        justify-content: center;
    }
    
    .btn-action {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
        background: transparent;
        text-decoration: none;
    }
    
    .btn-view {
        color: #3b82f6;
        background: #eff6ff;
    }
    
    .btn-view:hover {
        background: #dbeafe;
        transform: translateY(-2px);
        color: #2563eb;
    }
    
    .btn-edit {
        color: #f59e0b;
        background: #fffbeb;
    }
    
    .btn-edit:hover {
        background: #fef3c7;
        transform: translateY(-2px);
        color: #d97706;
    }
    
    .btn-delete {
        color: #ef4444;
        background: #fef2f2;
    }
    
    .btn-delete:hover {
        background: #fee2e2;
        transform: translateY(-2px);
        color: #dc2626;
    }

    /* Table Styling */
    .table {
        margin-bottom: 0;
    }
    
    .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }
    
    .table-hover tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8fafc;
        transform: scale(1.01);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 50px 20px;
    }

    /* Pagination Styling */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
    }
    
    .pagination {
        margin-bottom: 0;
        gap: 5px;
    }
    
    .page-link {
        border: none;
        color: #475569;
        border-radius: 10px !important;
        padding: 0.5rem 0.85rem;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s ease;
        background: #f8fafc;
    }
    
    .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
    }
    
    .page-link:hover {
        background-color: #e2e8f0;
        color: #1e293b;
        transform: translateY(-2px);
    }
    
    .page-item.disabled .page-link {
        background: #f1f5f9;
        color: #94a3b8;
    }

    /* Form Controls */
    .form-control, .form-select {
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        border-radius: 10px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .bg-light {
        background-color: #f8fafc !important;
    }

    /* Card Improvements */
    .card {
        transition: all 0.3s ease;
        border: none !important;
    }
    
    .card:hover {
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1) !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .table > :not(caption) > * > * {
            padding: 0.75rem 0.5rem;
        }
        
        .supplier-avatar {
            width: 36px;
            height: 36px;
            font-size: 14px;
        }
        
        .btn-action {
            width: 30px;
            height: 30px;
        }
        
        .status-badge, .gst-badge, .discount-badge, .total-badge {
            font-size: 10px;
            padding: 3px 8px;
        }
        
        .invoice-number {
            font-size: 11px;
            padding: 3px 8px;
        }
        
        .stats-icon {
            width: 45px;
            height: 45px;
        }
        
        .stats-icon i {
            font-size: 1.5rem !important;
        }
        
        h2 {
            font-size: 1.3rem;
        }
    }

    /* Custom Scrollbar */
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }
    
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Typography */
    .fw-semibold {
        font-weight: 600;
    }
    
    .text-gray-800 {
        color: #1e293b;
    }
    
    .rounded-4 {
        border-radius: 1rem !important;
    }
    
    .rounded-bottom-4 {
        border-bottom-left-radius: 1rem !important;
        border-bottom-right-radius: 1rem !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    
    // Confirm delete function
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this! This will also reverse stock and ledger entries.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
    
    // Auto-submit when per_page changes
    document.querySelectorAll('select[name="per_page"]').forEach(element => {
        element.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
    
    // Auto-submit when supplier changes
    document.querySelectorAll('select[name="supplier_id"]').forEach(element => {
        element.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
    
    // Auto-submit when date changes
    document.querySelectorAll('input[type="date"]').forEach(element => {
        element.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
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
</script>
@endpush