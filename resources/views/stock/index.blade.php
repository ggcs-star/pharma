@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-box-seam me-2 text-primary"></i>Stock Management
            </h2>
            <p class="text-muted mb-0">Manage your pharmaceutical inventory with batch tracking</p>
        </div>
        <nav aria-label="breadcrumb" class="mt-2 mt-md-0">
            <ol class="breadcrumb mb-0 bg-light p-2 px-3 rounded-3">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active fw-semibold">Stock Management</li>
            </ol>
        </nav>
    </div>

    {{-- Workflow Steps Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 workflow-card position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge bg-primary rounded-pill px-3 py-2">Step 1</span>
                </div>
                <div class="card-body p-4">
                    <div class="workflow-icon bg-primary-soft text-primary rounded-3 mb-3">
                        <i class="bi bi-cart-plus fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Purchase Entry</h5>
                    <p class="small text-muted mb-0">Stock In + Batch Creation</p>
                </div>
                <div class="progress rounded-0" style="height: 3px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 25%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 workflow-card">
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge bg-warning rounded-pill px-3 py-2">Step 2</span>
                </div>
                <div class="card-body p-4">
                    <div class="workflow-icon bg-warning-soft text-warning rounded-3 mb-3">
                        <i class="bi bi-tag fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-2">MRP Update</h5>
                    <p class="small text-muted mb-0">Verify Selling Price + GST</p>
                </div>
                <div class="progress rounded-0" style="height: 3px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 50%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 workflow-card">
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge bg-success rounded-pill px-3 py-2">Step 3</span>
                </div>
                <div class="card-body p-4">
                    <div class="workflow-icon bg-success-soft text-success rounded-3 mb-3">
                        <i class="bi bi-receipt fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Sales Entry</h5>
                    <p class="small text-muted mb-0">Strip / Loose Sales</p>
                </div>
                <div class="progress rounded-0" style="height: 3px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 75%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 workflow-card">
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge bg-danger rounded-pill px-3 py-2">Step 4</span>
                </div>
                <div class="card-body p-4">
                    <div class="workflow-icon bg-danger-soft text-danger rounded-3 mb-3">
                        <i class="bi bi-arrow-repeat fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Stock Return</h5>
                    <p class="small text-muted mb-0">Purchase / Sales Return</p>
                </div>
                <div class="progress rounded-0" style="height: 3px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Filter Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent py-3 border-bottom-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-0"><i class="bi bi-funnel me-2"></i>Filter Inventory</h5>
                    <p class="small text-muted mb-0 mt-1">Search and filter your stock items</p>
                </div>
                @if(request('search') || request('from_date') || request('to_date'))
                    <a href="{{ route('stock.index') }}" class="btn btn-outline-danger btn-sm rounded-pill">
                        <i class="bi bi-x-circle me-1"></i>Clear Filters
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body pt-0">
            <form method="GET" action="{{ route('stock.index') }}" class="row g-3">
                <div class="col-md-12 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-start-0" 
                               placeholder="Search by item name, code or category..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-calendar3"></i>
                        </span>
                        <input type="date" 
                               name="from_date" 
                               class="form-control" 
                               value="{{ request('from_date') }}"
                               placeholder="From Date">
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-calendar3"></i>
                        </span>
                        <input type="date" 
                               name="to_date" 
                               class="form-control" 
                               value="{{ request('to_date') }}"
                               placeholder="To Date">
                    </div>
                </div>
                
                <div class="col-md-12 col-lg-1">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Stock Table Card --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent py-3 border-bottom-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Stock Summary
                    </h5>
                    <p class="small text-muted mb-0 mt-1">Complete inventory with batch-wise details</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-pill px-3 py-2">
                        <i class="bi bi-box me-1"></i>Total Items: {{ $stocks->total() }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 5%"></th>
                            <th style="width: 25%">Item Details</th>
                            <th class="text-center" style="width: 12%">Purchased</th>
                            <th class="text-center" style="width: 12%">Sold</th>
                            <th class="text-center" style="width: 14%">Available Stock</th>
                            <th class="text-center" style="width: 15%">Batches</th>
                            <th class="text-center pe-4" style="width: 17%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                            @php
                                $hasBatches = count($batchStocks[$stock->id] ?? []) > 0;
                                $batchCount = count($batchStocks[$stock->id] ?? []);
                                $totalPurchased = $stock->total_purchase ?? 0;
                                $totalSold = $stock->total_sale ?? 0;
                                $availableStock = $stock->available_stock ?? 0;
                                $availableClass = $availableStock > 10 ? 'success' : ($availableStock > 0 ? 'warning' : 'danger');
                            @endphp
                            
                            {{-- Main Row --}}
                            <tr class="stock-row" data-stock-id="{{ $stock->id }}">
                                <td class="ps-4 text-center">
                                    @if($hasBatches)
                                        <button class="btn btn-sm toggle-batches rounded-circle p-0" 
                                                data-stock-id="{{ $stock->id }}"
                                                style="width: 28px; height: 28px;">
                                            <i class="bi bi-chevron-right"></i>
                                        </button>
                                    @endif
                                </td>
                                
                                {{-- Item Details --}}
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($stock->main_image_url)
                                            <img src="{{ $stock->main_image_url }}" 
                                                 alt="{{ $stock->name }}"
                                                 class="rounded-3 border"
                                                 style="width: 45px; height: 45px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center border"
                                                 style="width: 45px; height: 45px;">
                                                <i class="bi bi-capsule text-secondary fs-5"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('stock.show', $stock->id) }}" class="text-decoration-none fw-semibold text-dark stretched-link-hover">
                                                {{ $stock->name }}
                                            </a>
                                            @if(isset($stock->code) && $stock->code)
                                                <div class="small text-muted">
                                                    <i class="bi bi-upc-scan me-1"></i>Code: {{ $stock->code }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                
                                {{-- Total Purchased --}}
                                <td class="text-center">
                                    <span class="badge bg-info-soft text-info px-3 py-2 rounded-pill fw-semibold">
                                        <i class="bi bi-arrow-down me-1"></i>{{ number_format($totalPurchased) }}
                                    </span>
                                </td>
                                
                                {{-- Total Sold --}}
                                <td class="text-center">
                                    <span class="badge bg-warning-soft text-warning px-3 py-2 rounded-pill fw-semibold">
                                        <i class="bi bi-arrow-up me-1"></i>{{ number_format($totalSold) }}
                                    </span>
                                </td>
                                
                                {{-- Available Stock --}}
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="stock-indicator stock-{{ $availableClass }}"></div>
                                        <span class="fw-bold text-{{ $availableClass }} fs-5">
                                            {{ number_format($availableStock) }}
                                        </span>
                                    </div>
                                </td>
                                
                                {{-- Batch Count --}}
                                <td class="text-center">
                                    @if($hasBatches)
                                        <span class="badge bg-secondary-soft text-secondary px-3 py-2 rounded-pill">
                                            <i class="bi bi-layers me-1"></i>
                                            {{ $batchCount }} {{ $batchCount == 1 ? 'Batch' : 'Batches' }}
                                        </span>
                                    @else
                                        <span class="text-muted fst-italic small">
                                            <i class="bi bi-dash-circle me-1"></i>No batches
                                        </span>
                                    @endif
                                </td>
                                
                                {{-- Action Button --}}
                                <td class="pe-4 text-center">
                                    <a href="{{ route('stock.show', $stock->id) }}" 
                                       class="btn btn-primary btn-sm rounded-pill px-3">
                                        <i class="bi bi-eye me-1"></i>Manage Stock
                                    </a>
                                </td>
                            </tr>
                            
                            {{-- Batches Detail Row --}}
                            @if($hasBatches)
                                <tr class="batch-detail-row" id="batches-{{ $stock->id }}" style="display: none;">
                                    <td colspan="7" class="p-0">
                                        <div class="batch-container p-4">
                                            <div class="d-flex align-items-center gap-2 mb-3">
                                                <i class="bi bi-layers fs-5 text-primary"></i>
                                                <h6 class="fw-bold mb-0">Batch-wise Stock Details</h6>
                                                <span class="badge bg-light text-dark ms-2">{{ $batchCount }} batches</span>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-sm batch-table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Batch Code</th>
                                                            <th class="text-center">Purchased</th>
                                                            <th class="text-center">Sold</th>
                                                            <th class="text-center">Available</th>
                                                            <th class="text-center">Expiry Date</th>
                                                            <th class="text-center">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($batchStocks[$stock->id] ?? [] as $batch)
                                                            @php
                                                                $expiryDate = isset($batch->expiry_date) ? \Carbon\Carbon::parse($batch->expiry_date) : null;
                                                                $isExpired = $expiryDate ? $expiryDate->isPast() : false;
                                                                $isNearExpiry = $expiryDate ? ($expiryDate->diffInDays(now()) <= 30 && !$isExpired) : false;
                                                                $daysUntilExpiry = $expiryDate ? $expiryDate->diffInDays(now(), false) : 0;
                                                                $batchAvailable = $batch->available_stock ?? 0;
                                                                $batchAvailableClass = $batchAvailable > 10 ? 'success' : ($batchAvailable > 0 ? 'warning' : 'danger');
                                                            @endphp
                                                            
                                                            <tr>
                                                                <td class="fw-semibold">{{ $batch->batch_code ?? 'N/A' }}</td>
                                                                <td class="text-center">{{ number_format($batch->total_purchase ?? 0) }}</td>
                                                                <td class="text-center">{{ number_format($batch->total_sale ?? 0) }}</td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-{{ $batchAvailableClass }}-soft text-{{ $batchAvailableClass }} px-2 py-1 rounded-pill">
                                                                        {{ number_format($batchAvailable) }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-center {{ $isExpired ? 'text-danger fw-semibold' : ($isNearExpiry ? 'text-warning fw-semibold' : '') }}">
                                                                    @if($expiryDate)
                                                                        {{ $expiryDate->format('d M Y') }}
                                                                        @if($isNearExpiry && !$isExpired)
                                                                            <small class="d-block">({{ $daysUntilExpiry }} days left)</small>
                                                                        @endif
                                                                    @else
                                                                        —
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($isExpired)
                                                                        <span class="badge bg-danger rounded-pill">Expired</span>
                                                                    @elseif($isNearExpiry)
                                                                        <span class="badge bg-warning rounded-pill">Near Expiry</span>
                                                                    @elseif($expiryDate)
                                                                        <span class="badge bg-success rounded-pill">Valid</span>
                                                                    @else
                                                                        <span class="badge bg-secondary rounded-pill">No Expiry</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="mb-3">
                                            <i class="bi bi-box-seam display-1 text-muted opacity-25"></i>
                                        </div>
                                        <h5 class="fw-semibold mb-2">No stock items found</h5>
                                        <p class="text-muted mb-0">Try adjusting your search or filter criteria</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination Footer --}}
        @if($stocks->hasPages())
            <div class="card-footer bg-transparent py-3 border-top-0">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <small class="text-muted">
                        Showing {{ $stocks->firstItem() ?? 0 }} to {{ $stocks->lastItem() ?? 0 }} 
                        of {{ $stocks->total() }} entries
                    </small>
                    {{ $stocks->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- JavaScript for Toggle Functionality --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle batch toggle
    document.querySelectorAll('.toggle-batches').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const stockId = this.dataset.stockId;
            const batchRow = document.getElementById(`batches-${stockId}`);
            const icon = this.querySelector('i');
            
            if (batchRow && batchRow.style.display === 'none') {
                batchRow.style.display = 'table-row';
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-down');
                this.classList.add('btn-primary');
                this.classList.remove('btn-outline-primary');
                
                // Smooth animation
                batchRow.style.opacity = '0';
                setTimeout(() => {
                    batchRow.style.opacity = '1';
                }, 10);
            } else if (batchRow) {
                batchRow.style.display = 'none';
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-right');
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-primary');
            }
        });
    });
    
    // Row click toggle
    document.querySelectorAll('.stock-row').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('a') || e.target.closest('button')) {
                return;
            }
            const stockId = this.dataset.stockId;
            const toggleBtn = document.querySelector(`.toggle-batches[data-stock-id="${stockId}"]`);
            if (toggleBtn) {
                toggleBtn.click();
            }
        });
        row.style.cursor = 'pointer';
    });
});
</script>

{{-- Custom Styles --}}
<style>
:root {
    --primary-soft: rgba(13, 110, 253, 0.1);
    --warning-soft: rgba(255, 193, 7, 0.1);
    --success-soft: rgba(25, 135, 84, 0.1);
    --danger-soft: rgba(220, 53, 69, 0.1);
    --info-soft: rgba(13, 202, 240, 0.1);
    --secondary-soft: rgba(108, 117, 125, 0.1);
}

.bg-primary-soft { background-color: var(--primary-soft); }
.bg-warning-soft { background-color: var(--warning-soft); }
.bg-success-soft { background-color: var(--success-soft); }
.bg-danger-soft { background-color: var(--danger-soft); }
.bg-info-soft { background-color: var(--info-soft); }
.bg-secondary-soft { background-color: var(--secondary-soft); }

.workflow-card {
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}
.workflow-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}

.workflow-icon {
    width: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stock-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.stock-success { background-color: #198754; box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.2); }
.stock-warning { background-color: #ffc107; box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.2); }
.stock-danger { background-color: #dc3545; box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2); }

.batch-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-top: 2px solid #e9ecef;
    animation: slideDown 0.25s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.batch-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
}

.batch-table thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #dee2e6;
}

.batch-table tbody tr:hover {
    background-color: #f8f9fa;
}

.stock-row {
    transition: background-color 0.2s ease;
}

.stock-row:hover {
    background-color: #f8f9fa;
}

.batch-detail-row {
    background-color: #fafbfc;
}

.toggle-batches {
    transition: all 0.2s ease;
    line-height: 1;
}

.toggle-batches i {
    font-size: 0.9rem;
}

.empty-state {
    text-align: center;
    padding: 2rem;
}

/* Responsive table */
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

/* Pagination styling */
.pagination {
    margin-bottom: 0;
}

.pagination .page-link {
    border-radius: 8px;
    margin: 0 2px;
    border: none;
    color: #475569;
    padding: 0.5rem 0.85rem;
}

.pagination .page-item.active .page-link {
    background: #0d6efd;
    color: white;
}

.pagination .page-link:hover {
    background-color: #eef2ff;
    color: #0d6efd;
}

/* Badge styles */
.badge i {
    font-size: 0.85rem;
}

/* Link styles */
.stretched-link-hover:hover {
    color: #0d6efd !important;
    text-decoration: underline !important;
}

.btn-outline-primary.toggle-batches {
    border: 1px solid #dee2e6;
}

.btn-outline-primary.toggle-batches:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.btn-primary.toggle-batches {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}
</style>

{{-- Include Bootstrap Icons if not already in layout --}}
@if(!isset($hasBootstrapIcons))
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endif

@endsection