@extends('layouts.master')

@section('title', 'Purchase List')

@section('content')

<div class="container-fluid px-3 px-lg-4">
    <!-- Compact Page Header -->
    <div class="d-flex justify-content-between align-items-center pt-2 pb-1 mb-3 border-bottom">
        <div>
            <h5 class="mb-0 fw-semibold text-dark">Purchase Management</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active text-muted">Purchase List</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('purchase.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
            <i class="fas fa-plus-circle me-1"></i> New Purchase
        </a>
    </div>

    <!-- Compact Statistics Cards -->
    <div class="row g-2 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-2 p-xl-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small fw-semibold">Total Purchases</span>
                            <h4 class="mb-0 fw-bold mt-1">{{ $totalPurchases ?? $purchases->total() }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-2 p-2">
                            <i class="fas fa-shopping-cart text-primary fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-2 p-xl-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small fw-semibold">Total Amount</span>
                            <h4 class="mb-0 fw-bold mt-1">₹ {{ number_format($totalAmount ?? $purchases->sum('net_amount'), 2) }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-2 p-2">
                            <i class="fas fa-rupee-sign text-success fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-2 p-xl-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small fw-semibold">Total GST</span>
                            <h4 class="mb-0 fw-bold mt-1">₹ {{ number_format($totalGST ?? $purchases->sum('total_gst'), 2) }}</h4>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-2 p-2">
                            <i class="fas fa-percent text-info fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-2 p-xl-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small fw-semibold">Total Discount</span>
                            <h4 class="mb-0 fw-bold mt-1">₹ {{ number_format($totalDiscount ?? $purchases->sum('total_discount'), 2) }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-2 p-2">
                            <i class="fas fa-tag text-warning fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compact Search and Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('purchase.index') }}" id="filterForm">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Search Invoice</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search fa-sm"></i>
                            </span>
                            <input type="text" name="search" class="form-control form-control-sm bg-light" 
                                   placeholder="Invoice number..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Supplier</label>
                        <select name="supplier_id" class="form-select form-select-sm bg-light">
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
                        <label class="form-label small text-muted mb-1">From Date</label>
                        <input type="date" name="from_date" class="form-control form-control-sm bg-light" 
                               value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">To Date</label>
                        <input type="date" name="to_date" class="form-control form-control-sm bg-light" 
                               value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill">
                                <i class="fas fa-filter fa-sm me-1"></i> Filter
                            </button>
                            <a href="{{ route('purchase.index') }}" class="btn btn-sm btn-outline-secondary w-100 rounded-pill">
                                <i class="fas fa-undo-alt fa-sm me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Compact Purchases Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-2 ps-3 small fw-semibold text-muted" width="5%">ID</th>
                            <th class="py-2 small fw-semibold text-muted" width="12%">Invoice No</th>
                            <th class="py-2 small fw-semibold text-muted" width="20%">Supplier</th>
                            <th class="py-2 small fw-semibold text-muted" width="12%">Date</th>
                            <th class="py-2 text-end small fw-semibold text-muted" width="10%">GST (₹)</th>
                            <th class="py-2 text-end small fw-semibold text-muted" width="10%">Discount (₹)</th>
                            <th class="py-2 text-end small fw-semibold text-muted" width="10%">Total (₹)</th>
                            <th class="py-2 text-center small fw-semibold text-muted" width="10%">Status</th>
                            <th class="py-2 text-center pe-3 small fw-semibold text-muted" width="11%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                        <tr>
                            <td class="align-middle ps-3 small">#{{ $purchase->id }}</td>
                            <td class="align-middle">
                                <span class="fw-semibold text-primary small">
                                    PUR-{{ str_pad($purchase->invoice_number, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-building text-muted fa-xs"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $purchase->supplier->name ?? 'N/A' }}</div>
                                        <small class="text-muted">ID: {{ $purchase->supplier_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="small">
                                    <i class="far fa-calendar-alt me-1 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d-m-Y') }}
                                </div>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($purchase->created_at)->diffForHumans() }}
                                </small>
                            </td>
                            <td class="align-middle text-end small text-info fw-semibold">
                                ₹ {{ number_format($purchase->total_gst, 2) }}
                            </td>
                            <td class="align-middle text-end small text-warning fw-semibold">
                                ₹ {{ number_format($purchase->total_discount, 2) }}
                            </td>
                            <td class="align-middle text-end small fw-bold text-success">
                                ₹ {{ number_format($purchase->net_amount, 2) }}
                            </td>
                            <td class="align-middle text-center">
                                @if(strtolower($purchase->payment_type ?? 'pending') == 'cash')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 small fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i> Paid
                                    </span>
                                @elseif(strtolower($purchase->payment_type ?? 'pending') == 'pending')
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 small fw-semibold">
                                        <i class="fas fa-clock me-1"></i> Pending
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-1 small fw-semibold">
                                        <i class="fas fa-credit-card me-1"></i> {{ $purchase->payment_type ?? 'Unknown' }}
                                    </span>
                                @endif
                            </td>
                            <td class="align-middle text-center pe-3">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('purchase.show', $purchase->id) }}" 
                                       class="btn btn-sm btn-outline-info rounded-start-pill" 
                                       data-bs-toggle="tooltip" 
                                       title="View Details">
                                        <i class="fas fa-eye fa-xs"></i>
                                    </a>
                                    <a href="{{ route('purchase.edit', $purchase->id) }}" 
                                       class="btn btn-sm btn-outline-warning"
                                       data-bs-toggle="tooltip" 
                                       title="Edit Purchase">
                                        <i class="fas fa-edit fa-xs"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger rounded-end-pill" 
                                            onclick="confirmDelete({{ $purchase->id }})"
                                            data-bs-toggle="tooltip" 
                                            title="Delete Purchase">
                                        <i class="fas fa-trash-alt fa-xs"></i>
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
                            <td colspan="9" class="text-center py-4">
                                <div class="empty-state py-3">
                                    <i class="fas fa-shopping-cart fa-3x text-muted mb-2"></i>
                                    <h6 class="text-muted">No Purchases Found</h6>
                                    <p class="small text-muted mb-2">Click the "New Purchase" button to create your first purchase.</p>
                                    <a href="{{ route('purchase.create') }}" class="btn btn-sm btn-primary rounded-pill mt-1">
                                        <i class="fas fa-plus-circle me-1"></i> Create New Purchase
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
        <div class="card-footer bg-white border-0 py-2 rounded-bottom-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <small class="text-muted">
                        Showing {{ $purchases->firstItem() }} to {{ $purchases->lastItem() }} 
                        of {{ $purchases->total() }} entries
                    </small>
                </div>
                <div>
                    {{ $purchases->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
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
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
    
    // Auto-submit form when filter changes (optional)
    document.querySelectorAll('#filterForm select, #filterForm input[type="date"]').forEach(element => {
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

@push('styles')
<style>
    /* Compact & Modern Styles */
    .avatar-sm {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Table compactness */
    .table > :not(caption) > * > * {
        padding: 0.5rem 0.5rem;
        vertical-align: middle;
    }
    
    /* Action buttons compact */
    .btn-group-sm > .btn {
        padding: 0.2rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.2rem;
    }
    
    .btn-group .btn {
        margin: 0 1px;
    }
    
    /* Empty state */
    .empty-state {
        text-align: center;
    }
    
    /* Card hover effect */
    .card {
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
    }
    
    /* Badge styling */
    .badge {
        font-weight: 500;
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
    }
    
    /* Table hover effect */
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.04);
        cursor: pointer;
    }
    
    /* Pagination styling */
    .pagination {
        margin-bottom: 0;
        gap: 2px;
    }
    
    .page-link {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
        border-radius: 0.375rem !important;
        color: #4a5568;
        border: 1px solid #e2e8f0;
    }
    
    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
    
    /* Form controls compact */
    .form-control-sm, .form-select-sm {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
    
    .input-group-sm > .form-control {
        font-size: 0.8rem;
    }
    
    /* Button styling */
    .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .btn-outline-info:hover, 
    .btn-outline-warning:hover, 
    .btn-outline-danger:hover {
        color: white;
    }
    
    .btn-outline-info:hover {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
    }
    
    .btn-outline-warning:hover {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }
    
    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table th, .table td {
            font-size: 0.75rem;
            padding: 0.4rem 0.3rem;
        }
        
        .badge {
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
        }
        
        .avatar-sm {
            width: 24px;
            height: 24px;
        }
        
        h4 {
            font-size: 1.2rem;
        }
        
        .btn-group .btn {
            padding: 0.15rem 0.4rem;
        }
    }
    
    /* Custom scrollbar for table */
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }
    
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Soft gradient backgrounds for stats */
    .bg-primary.bg-opacity-10 {
        background: linear-gradient(135deg, rgba(13,110,253,0.1), rgba(13,110,253,0.05)) !important;
    }
    
    .bg-success.bg-opacity-10 {
        background: linear-gradient(135deg, rgba(25,135,84,0.1), rgba(25,135,84,0.05)) !important;
    }
    
    .bg-info.bg-opacity-10 {
        background: linear-gradient(135deg, rgba(13,202,240,0.1), rgba(13,202,240,0.05)) !important;
    }
    
    .bg-warning.bg-opacity-10 {
        background: linear-gradient(135deg, rgba(255,193,7,0.1), rgba(255,193,7,0.05)) !important;
    }
    
    /* Status badge backgrounds */
    .bg-success.bg-opacity-10 {
        background: rgba(25, 135, 84, 0.1) !important;
        color: #198754 !important;
    }
    
    .bg-danger.bg-opacity-10 {
        background: rgba(220, 53, 69, 0.1) !important;
        color: #dc3545 !important;
    }
    
    .bg-secondary.bg-opacity-10 {
        background: rgba(108, 117, 125, 0.1) !important;
        color: #6c757d !important;
    }
    
    /* Typography improvements */
    .fw-semibold {
        font-weight: 600;
    }
    
    .small {
        font-size: 0.8rem;
    }
    
    .text-muted {
        color: #6c757d !important;
    }
    
    /* Subtle box shadows */
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05) !important;
    }
    
    /* Border radius */
    .rounded-3 {
        border-radius: 0.5rem !important;
    }
    
    .rounded-pill {
        border-radius: 50rem !important;
    }
</style>
@endpush