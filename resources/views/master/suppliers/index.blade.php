@extends('layouts.master')

@section('title', 'Suppliers')

@section('content')

<div class="container-fluid px-4 py-3">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-truck text-primary me-2"></i>Suppliers
            </h1>
            <p class="text-muted small mb-0">Manage your supplier database</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus-circle me-2"></i>Add Supplier
        </a>
    </div>

    <!-- Stats Cards -->
    @php
        $totalSuppliers = \App\Models\Supplier::count();
        $activeSuppliers = \App\Models\Supplier::count(); // Remove status filter if column doesn't exist
        $totalBalance = 0;
        foreach($suppliers as $supplier) {
            $totalBalance += $supplier->ledgers->sum('debit') - $supplier->ledgers->sum('credit');
        }
        $pendingPayments = \App\Models\Supplier::whereHas('ledgers', function($q) {
            $q->havingRaw('SUM(debit) - SUM(credit) > 0');
        })->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Suppliers</h6>
                            <h2 class="mb-0 fw-bold text-primary">{{ $totalSuppliers }}</h2>
                        </div>
                        <div class="rounded-3 p-3" style="background: #eef2ff;">
                            <i class="fas fa-truck fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Balance</h6>
                            <h2 class="mb-0 fw-bold text-info">₹ {{ number_format($totalBalance, 0) }}</h2>
                        </div>
                        <div class="rounded-3 p-3" style="background: #ecfeff;">
                            <i class="fas fa-rupee-sign fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Pending Payments</h6>
                            <h2 class="mb-0 fw-bold text-warning">{{ $pendingPayments }}</h2>
                        </div>
                        <div class="rounded-3 p-3" style="background: #fefce8;">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <form method="GET" action="{{ route('suppliers.index') }}" id="filterForm">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="position-relative">
                            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" 
                                   name="search" 
                                   class="form-control rounded-pill ps-5" 
                                   placeholder="Search by name, code, GST, phone..." 
                                   value="{{ request('search') }}"
                                   style="background: #f8f9fa;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="balance_filter" class="form-select rounded-pill" style="background: #f8f9fa;" onchange="this.form.submit()">
                            <option value="">All Balances</option>
                            <option value="positive" {{ request('balance_filter') == 'positive' ? 'selected' : '' }}>📈 Positive Balance</option>
                            <option value="negative" {{ request('balance_filter') == 'negative' ? 'selected' : '' }}>📉 Negative Balance</option>
                            <option value="zero" {{ request('balance_filter') == 'zero' ? 'selected' : '' }}>⚖️ Zero Balance</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="sort_by" class="form-select rounded-pill" style="background: #f8f9fa;" onchange="this.form.submit()">
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Sort by Name</option>
                            <option value="supplier_code" {{ request('sort_by') == 'supplier_code' ? 'selected' : '' }}>Sort by Code</option>
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="sort_order" class="form-select rounded-pill" style="background: #f8f9fa;" onchange="this.form.submit()">
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>↑ Ascending</option>
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>↓ Descending</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <select name="per_page" class="form-select rounded-pill" style="background: #f8f9fa;" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        @if(request()->anyFilled(['search', 'balance_filter']))
                            <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-light rounded-pill">
                                <i class="fas fa-times me-1"></i> Clear Filters
                            </a>
                        @endif
                        <small class="text-muted ms-2">
                            <i class="fas fa-info-circle me-1"></i>
                            Showing {{ $suppliers->firstItem() ?? 0 }} to {{ $suppliers->lastItem() ?? 0 }} of {{ $suppliers->total() }} suppliers
                        </small>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3 rounded-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-3 rounded-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-uppercase small text-muted">
                            <th width="60" class="py-3"><i class="fas fa-hashtag me-1"></i> #</th>
                            <th width="100" class="py-3"><i class="fas fa-barcode me-1"></i> Code</th>
                            <th class="py-3"><i class="fas fa-building me-1"></i> Supplier</th>
                            <th width="130" class="py-3"><i class="fas fa-file-invoice me-1"></i> GSTIN</th>
                            <th width="100" class="py-3"><i class="fas fa-phone me-1"></i> Phone</th>
                            <th width="130" class="py-3"><i class="fas fa-prescription-bottle me-1"></i> Drug License</th>
                            <th width="130" class="py-3"><i class="fas fa-chart-line me-1"></i> T. Balance</th>
                            <th width="100" class="py-3 text-center"><i class="fas fa-cog me-1"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $index => $supplier)
                            @php
                                $balance = $supplier->ledgers->sum('debit') - $supplier->ledgers->sum('credit');
                            @endphp
                            <tr>
                                <td class="fw-semibold text-muted text-center">{{ $suppliers->firstItem() + $index }}</td>
                                <td class="text-center">
                                    <span class="supplier-code-badge">{{ $supplier->supplier_code ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="supplier-avatar">
                                            <i class="fas fa-truck"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $supplier->name }}</div>
                                            @if($supplier->email)
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope me-1"></i>{{ $supplier->email }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($supplier->gst_in)
                                        <span class="gst-badge">{{ $supplier->gst_in }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($supplier->phone)
                                        <i class="fas fa-phone-alt text-muted me-1"></i>{{ $supplier->phone }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($supplier->drug_license)
                                        <small class="text-muted">{{ $supplier->drug_license }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($balance > 0)
                                        <span class="balance-positive">
                                            <i class="fas fa-arrow-up me-1"></i>₹ {{ number_format($balance, 2) }}
                                        </span>
                                    @elseif($balance < 0)
                                        <span class="balance-negative">
                                            <i class="fas fa-arrow-down me-1"></i>₹ {{ number_format(abs($balance), 2) }}
                                        </span>
                                    @else
                                        <span class="balance-zero">
                                            <i class="fas fa-equals me-1"></i>₹ 0.00
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('suppliers.edit', $supplier->id) }}" 
                                           class="btn-action btn-edit" 
                                           data-bs-toggle="tooltip" 
                                           title="Edit Supplier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn-action btn-delete" 
                                                title="Delete Supplier"
                                                onclick="confirmDelete({{ $supplier->id }}, '{{ addslashes($supplier->name) }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $supplier->id }}" 
                                          action="{{ route('suppliers.destroy', $supplier->id) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-truck-slash fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">No Suppliers Found</h5>
                                        <p class="text-muted small mb-3">Get started by adding your first supplier</p>
                                        <a href="{{ route('suppliers.create') }}" class="btn btn-primary rounded-pill">
                                            <i class="fas fa-plus me-2"></i>Add Supplier
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center p-4 border-top">
                <div class="small text-muted">
                    <i class="fas fa-chart-line me-1"></i>
                    Showing <strong>{{ $suppliers->firstItem() ?? 0 }}</strong> to <strong>{{ $suppliers->lastItem() ?? 0 }}</strong> 
                    of <strong>{{ $suppliers->total() }}</strong> results
                </div>
                <div>
                    {{ $suppliers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Supplier Avatar */
    .supplier-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 20px;
    }

    /* Supplier Code Badge */
    .supplier-code-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        font-family: monospace;
        display: inline-block;
    }

    /* GST Badge */
    .gst-badge {
        background: #eef2ff;
        color: #4338ca;
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 500;
        font-family: monospace;
        display: inline-block;
    }

    /* Balance Styles */
    .balance-positive {
        background: #fef2f2;
        color: #dc2626;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        justify-content: flex-end;
    }
    
    .balance-negative {
        background: #ecfdf5;
        color: #059669;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        justify-content: flex-end;
    }
    
    .balance-zero {
        background: #f1f5f9;
        color: #64748b;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        justify-content: flex-end;
    }

    /* Action Buttons */
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

    /* Empty State */
    .empty-state {
        padding: 40px 20px;
    }

    /* Table Styling */
    .table-hover tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Card Improvements */
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
    }

    /* Form Controls */
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Pagination Styling */
    .pagination {
        margin-bottom: 0;
    }
    
    .page-link {
        border: none;
        color: #475569;
        border-radius: 8px !important;
        margin: 0 2px;
    }
    
    .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .page-link:hover {
        background-color: #f1f5f9;
        color: #1e293b;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Auto-submit form when filters change
document.addEventListener('DOMContentLoaded', function() {
    const filterSelects = document.querySelectorAll('#filterForm select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    // Debounce search input
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if(searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        });
    }

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    if(tooltipTriggerList.length) {
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

// Confirm Delete Function
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Are you sure?',
        text: `Delete supplier "${name}"? This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    });
}

// Show success/error messages
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33'
    });
@endif
</script>
@endpush