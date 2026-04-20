@extends('layouts.master')

@section('title', 'Purchase Orders')

@section('content')

<div class="container-fluid px-4 py-3">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-semibold">Purchase Orders</h1>
            <p class="text-muted small mb-0">Manage all purchase orders and track status</p>
        </div>
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary px-4">
            <i class="fas fa-plus-circle me-2"></i>Create Purchase Order
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small text-uppercase mb-1">Total Orders</p>
                            <h2 class="mb-0 fw-bold">{{ $orders->total() }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-shopping-cart text-primary fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small text-uppercase mb-1">Pending Orders</p>
                            <h2 class="mb-0 fw-bold text-warning">
                                {{ $orders->where('status', 'pending')->count() }}
                            </h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clock text-warning fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small text-uppercase mb-1">Delivered</p>
                            <h2 class="mb-0 fw-bold text-success">
                                {{ $orders->where('status', 'delivered')->count() }}
                            </h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small text-uppercase mb-1">Cancelled</p>
                            <h2 class="mb-0 fw-bold text-danger">
                                {{ $orders->where('status', 'cancelled')->count() }}
                            </h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-times-circle text-danger fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('purchase-orders.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-medium">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Order number, supplier..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-medium">From Date</label>
                        <input type="date" 
                               name="from_date" 
                               class="form-control" 
                               value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-medium">To Date</label>
                        <input type="date" 
                               name="to_date" 
                               class="form-control" 
                               value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-medium">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="dispatched" {{ request('status') == 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-medium">Per Page</label>
                        <select name="per_page" class="form-select">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 entries</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 entries</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 entries</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 entries</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary px-4">
                                <i class="fas fa-undo-alt me-2"></i>Reset
                            </a>
                            <div class="ms-auto">
                                <small class="text-muted">
                                    Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} 
                                    of {{ $orders->total() }} entries
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="ps-3">#</th>
                        <th width="15%">Order No</th>
                        <th width="12%">Date</th>
                        <th width="25%">Supplier</th>
                        <th width="10%">Items</th>
                        <th width="10%">Total</th>
                        <th width="10%" class="text-center">Status</th>
                        <th width="13%" class="text-center pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'confirmed' => 'primary',
                            'processing' => 'info',
                            'dispatched' => 'info',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            'rejected' => 'secondary',
                        ];
                        $statusIcons = [
                            'pending' => 'fa-clock',
                            'confirmed' => 'fa-check-circle',
                            'processing' => 'fa-cog',
                            'dispatched' => 'fa-truck',
                            'delivered' => 'fa-check-double',
                            'cancelled' => 'fa-times-circle',
                            'rejected' => 'fa-ban',
                        ];
                    @endphp

                    @forelse($orders as $order)
                    <tr>
                        <td class="ps-3">#{{ $order->id }}</td>
                        <td>
                            <span class="fw-semibold text-primary">
                                {{ $order->order_number }}
                            </span>
                        </td>
                        <td>
                            <div>{{ \Carbon\Carbon::parse($order->order_date)->format('d M, Y') }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($order->order_date)->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-2">
                                    <i class="fas fa-building text-secondary fa-sm"></i>
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $order->supplier->name ?? 'N/A' }}</div>
                                    <small class="text-muted">Code: {{ $order->supplier->supplier_code ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                {{ $order->items_count ?? 0 }} items
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold text-success">
                                ₹ {{ number_format($order->total_amount ?? 0, 2) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$order->status] ?? 'secondary' }} px-3 py-1">
                                <i class="fas {{ $statusIcons[$order->status] ?? 'fa-info-circle' }} me-1"></i>
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="text-center pe-3">
                            <div class="btn-group">
                                <a href="{{ route('purchase-orders.show', $order->id) }}" 
                                   class="btn btn-sm btn-outline-info" 
                                   data-bs-toggle="tooltip" 
                                   title="View Order">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($order->status == 'dispatched')
                                    <form method="POST" action="{{ url('/purchase-orders/'.$order->id.'/status') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="delivered">
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-success" 
                                                data-bs-toggle="tooltip" 
                                                title="Mark Delivered">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    </form>
                                @endif

                                @if(!in_array($order->status, ['delivered','cancelled','rejected']))
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
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
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3 d-block"></i>
                            <h5 class="text-muted">No Purchase Orders Found</h5>
                            <p class="text-muted small mb-3">Click the "Create Purchase Order" button to create your first order.</p>
                            <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>Create Purchase Order
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->isNotEmpty())
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-center">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Bootstrap Subtle Colors */
    .bg-primary-subtle { background-color: #cfe2ff !important; }
    .bg-success-subtle { background-color: #d1fae5 !important; }
    .bg-danger-subtle { background-color: #fee2e2 !important; }
    .bg-warning-subtle { background-color: #fef3c7 !important; }
    .bg-info-subtle { background-color: #cffafe !important; }
    .bg-secondary-subtle { background-color: #f1f5f9 !important; }
    
    .text-primary-subtle { color: #084298 !important; }
    .text-success-subtle { color: #059669 !important; }
    .text-danger-subtle { color: #dc2626 !important; }
    .text-warning-subtle { color: #d97706 !important; }
    .text-info-subtle { color: #0891b2 !important; }
    .text-secondary-subtle { color: #475569 !important; }
    
    /* Card Styles */
    .card {
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
    }
    
    /* Table Styles */
    .table th {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom-width: 1px;
        background-color: #f8f9fa;
    }
    
    .table td {
        vertical-align: middle;
        font-size: 0.85rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Badge Styles */
    .badge {
        font-weight: 500;
        font-size: 0.7rem;
    }
    
    /* Button Group */
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .btn-group .btn:first-child {
        border-radius: 0.25rem 0 0 0.25rem;
    }
    
    .btn-group .btn:last-child {
        border-radius: 0 0.25rem 0.25rem 0;
    }
    
    .btn-group .btn:not(:first-child):not(:last-child) {
        border-radius: 0;
    }
    
    /* Form Controls */
    .form-control, .form-select {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
    }
    
    /* Pagination */
    .pagination {
        margin-bottom: 0;
        gap: 0.25rem;
    }
    
    .page-link {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        color: #495057;
        padding: 0.375rem 0.75rem;
        font-size: 0.85rem;
    }
    
    .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #212529;
    }
    
    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
    
    .page-item.disabled .page-link {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        color: #6c757d;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .table td, .table th {
            font-size: 0.75rem;
        }
        
        .btn-group .btn {
            padding: 0.2rem 0.4rem;
        }
        
        h2 {
            font-size: 1.3rem;
        }
        
        h1 {
            font-size: 1.5rem;
        }
    }
    
    /* Utility Classes */
    .fw-medium {
        font-weight: 500;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Auto-submit when per_page changes
    document.querySelector('select[name="per_page"]')?.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
    
    // Auto-submit when status changes
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
    
    // Cancel order function
    function cancelOrder(id) {
        Swal.fire({
            title: 'Cancel Purchase Order?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancel-form-' + id).submit();
            }
        });
    }
</script>
@endpush