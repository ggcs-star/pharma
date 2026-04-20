@extends('layouts.master')

@section('title', 'Sales List')

@section('content')

<div class="container-fluid px-4 py-3">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-semibold">Sales Management</h1>
            <p class="text-muted small mb-0">Manage all your sales transactions</p>
        </div>
        <a href="{{ route('sales.create') }}" class="btn btn-primary px-4">
            <i class="fas fa-plus-circle me-2"></i>New Sale
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small text-uppercase mb-1">Total Sales</p>
                            <h2 class="mb-0 fw-bold">{{ $sales->total() }}</h2>
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
                            <p class="text-muted small text-uppercase mb-1">Total Revenue</p>
                            <h2 class="mb-0 fw-bold text-success">₹ {{ number_format($sales->sum('net_amount'), 2) }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-rupee-sign text-success fa-lg"></i>
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
                            <p class="text-muted small text-uppercase mb-1">Today's Sales</p>
                            <h2 class="mb-0 fw-bold text-info">
                                ₹ {{ number_format($sales->where('bill_date', now()->toDateString())->sum('net_amount'), 2) }}
                            </h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-calendar-day text-info fa-lg"></i>
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
                            <p class="text-muted small text-uppercase mb-1">Avg. Sale Value</p>
                            <h2 class="mb-0 fw-bold text-warning">
                                ₹ {{ number_format($sales->avg('net_amount') ?? 0, 2) }}
                            </h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chart-line text-warning fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('sales.index') }}" id="filterForm">
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
                                   placeholder="Bill number, customer..." 
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
                        <label class="form-label small fw-medium">Customer</label>
                        <select name="customer_id" class="form-select">
                            <option value="">All Customers</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" 
                                    {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
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
                            <a href="{{ route('sales.index') }}" class="btn btn-secondary px-4">
                                <i class="fas fa-undo-alt me-2"></i>Reset
                            </a>
                            <div class="ms-auto">
                                <small class="text-muted">
                                    Showing {{ $sales->firstItem() ?? 0 }} to {{ $sales->lastItem() ?? 0 }} 
                                    of {{ $sales->total() }} entries
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="ps-3">#</th>
                        <th width="15%">Bill No</th>
                        <th width="20%">Customer</th>
                        <th width="12%">Bill Date</th>
                        <th width="10%">Items</th>
                        <th width="10%" class="text-end">Subtotal</th>
                        <th width="10%" class="text-end">Discount</th>
                        <th width="10%" class="text-end">Net Total</th>
                        <th width="8%" class="text-center pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td class="ps-3">{{ $sale->id }}</td>
                        <td>
                            <span class="fw-semibold text-primary">
                                <i class="fas fa-receipt me-1"></i>
                                {{ $sale->bill_number }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-2">
                                    <i class="fas fa-user text-secondary fa-sm"></i>
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $sale->customer->name ?? 'Walk-in Customer' }}</div>
                                    <small class="text-muted">
                                        {{ $sale->customer->phone ?? 'No phone' }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>{{ \Carbon\Carbon::parse($sale->bill_date)->format('d M, Y') }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($sale->created_at)->diffForHumans() }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                <i class="fas fa-box me-1"></i>
                                {{ $sale->items_count ?? $sale->items->count() ?? 0 }} items
                            </span>
                        </td>
                        <td class="text-end text-muted">
                            ₹ {{ number_format($sale->subtotal ?? $sale->total_amount, 2) }}
                        </td>
                        <td class="text-end text-warning">
                            ₹ {{ number_format($sale->discount ?? $sale->total_discount, 2) }}
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-success">
                                ₹ {{ number_format($sale->net_amount, 2) }}
                            </span>
                        </td>
                        <td class="text-center pe-3">
                            <div class="btn-group">
                                <a href="{{ route('sales.show', $sale->id) }}" 
                                   class="btn btn-sm btn-outline-info" 
                                   data-bs-toggle="tooltip" 
                                   title="View Invoice">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('sales.invoice', $sale->id) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   data-bs-toggle="tooltip" 
                                   title="Print Invoice">
                                    <i class="fas fa-print"></i>
                                </a>
                                @if($sale->net_amount <= 0)
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        onclick="confirmDelete({{ $sale->id }}, '{{ $sale->bill_number }}')"
                                        data-bs-toggle="tooltip" 
                                        title="Delete Sale">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @endif
                            </div>
                            @if($sale->net_amount <= 0)
                            <form id="delete-form-{{ $sale->id }}" 
                                  action="{{ route('sales.destroy', $sale->id) }}" 
                                  method="POST" 
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3 d-block"></i>
                            <h5 class="text-muted">No Sales Found</h5>
                            <p class="text-muted small mb-3">Click the "New Sale" button to create your first sale.</p>
                            <a href="{{ route('sales.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>Create New Sale
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($sales->isNotEmpty())
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-center">
                {{ $sales->appends(request()->query())->links() }}
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
    
    // Auto-submit when customer changes
    document.querySelector('select[name="customer_id"]')?.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
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
    
    // Confirm delete function
    function confirmDelete(id, billNumber) {
        Swal.fire({
            title: 'Delete Sale?',
            text: `Sale "${billNumber}" will be deleted. This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endpush