@extends('layouts.master')

@section('content')
<div class="container py-4">
    
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-box-seam me-2"></i>Stock Management
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Stock Management</li>
            </ol>
        </nav>
    </div>

    {{-- Search and Filter Card --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('stock.index') }}" class="row g-3">
                {{-- Search Input --}}
                <div class="col-md-6 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Search by item name, code or category..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                
                {{-- From Date --}}
                <div class="col-md-3 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-calendar"></i>
                        </span>
                        <input type="date" 
                               name="from_date" 
                               class="form-control" 
                               value="{{ request('from_date') }}"
                               placeholder="From Date">
                    </div>
                </div>
                
                {{-- To Date --}}
                <div class="col-md-3 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-calendar"></i>
                        </span>
                        <input type="date" 
                               name="to_date" 
                               class="form-control" 
                               value="{{ request('to_date') }}"
                               placeholder="To Date">
                    </div>
                </div>
                
                {{-- Action Buttons --}}
                <div class="col-md-12 col-lg-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel me-1"></i>Apply Filters
                        </button>
                        
                        @if(request('search') || request('from_date') || request('to_date'))
                            <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Clear All
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Stock Table Card --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-table me-2"></i>Stock Summary
                </h5>
                <span class="badge bg-primary">
                    Total Items: {{ $stocks->total() }}
                </span>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 5%"></th>
                            <th style="width: 25%">Item </th>
                            <th class="text-center" style="width: 15%">Total Purchased</th>
                            <th class="text-center" style="width: 15%">Total Sold</th>
                            <th class="text-center" style="width: 15%">Available Stock</th>
                            <th class="pe-4" style="width: 15%">Batches</th>
                            <th class="pe-4" style="width: 15%">Actions</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                            @php
                                $hasBatches = count($batchStocks[$stock->id] ?? []) > 0;
                                $batchCount = count($batchStocks[$stock->id] ?? []);
                            @endphp
                            
                            {{-- Main Row --}}
                            <tr class="stock-row" data-stock-id="{{ $stock->id }}">
                                {{-- Expand/Collapse Button --}}
                                <td class="ps-4">
                                    @if($hasBatches)
                                        <button class="btn btn-sm btn-outline-primary toggle-batches" 
                                                data-stock-id="{{ $stock->id }}">
                                            <i class="bi bi-chevron-right"></i>
                                        </button>
                                    @endif
                                </td>
                                
                                {{-- Item Name with Link --}}
      <td class="fw-medium">
    <a href="{{ route('stock.show', $stock->id) }}" class="text-decoration-none fw-medium d-flex align-items-center gap-2">

        <!-- Image -->
       @if($stock->main_image_url)
    <img src="{{ $stock->main_image_url }}" 
         alt="{{ $stock->name }}"
         class="rounded"
         style="width:32px; height:32px; object-fit:cover;">
@else
    <div class="bg-light rounded d-flex align-items-center justify-content-center"
         style="width:32px; height:32px;">
        <i class="bi bi-image text-muted small"></i>
    </div>
@endif

        <!-- Name -->
        <span>
            {{ $stock->name }}
            <i class="bi bi-box-arrow-up-right ms-1 small text-muted"></i>
        </span>

    </a>
</td>                          
                                {{-- Total Purchased --}}
                                <td class="text-center">
                                    <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                        {{ number_format($stock->total_purchase) }}
                                    </span>
                                </td>
                                
                                {{-- Total Sold --}}
                                <td class="text-center">
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">
                                        {{ number_format($stock->total_sold) }}
                                    </span>
                                </td>
                                
                                {{-- Available Stock with color coding --}}
                                <td class="text-center">
                                    @php
                                        $availableClass = $stock->available_stock > 10 
                                            ? 'success' 
                                            : ($stock->available_stock > 0 ? 'warning' : 'danger');
                                    @endphp
                                    <span class="badge bg-{{ $availableClass }} bg-opacity-10 text-{{ $availableClass }} px-3 py-2 fw-bold">
                                        {{ number_format($stock->available_stock) }}
                                    </span>
                                </td>
                                
                                {{-- Batch Count Badge --}}
                                <td class="pe-4">
                                    @if($hasBatches)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                                            <i class="bi bi-layers me-1"></i>
                                            {{ $batchCount }} {{ Str::plural('Batch', $batchCount) }}
                                        </span>
                                    @else
                                        <span class="text-muted fst-italic">
                                            <i class="bi bi-dash-circle me-1"></i>No batches
                                        </span>
                                    @endif
                                </td>
                                <td>
    <a href="{{ route('stock.show', $stock->id) }}" 
       class="fw-medium text-decoration-none text-primary d-inline-flex align-items-center gap-1">
        <span>View Details</span>
    </a>
</td>
                            </tr>
                            
                            {{-- Batches Detail Row (Hidden by default) --}}
                            @if($hasBatches)
                                <tr class="batch-detail-row" id="batches-{{ $stock->id }}" style="display: none;">
                                    <td colspan="7" class="p-0">
                                        <div class="batch-container p-3">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered mb-0 batch-table">
                                                    <thead class="table-secondary">
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
                                                                $expiryDate = \Carbon\Carbon::parse($batch->expiry_date);
                                                                $isExpired = $expiryDate->isPast();
                                                                $isNearExpiry = $expiryDate->diffInDays(now()) <= 30 && !$isExpired;
                                                                $daysUntilExpiry = $expiryDate->diffInDays(now(), false);
                                                                
                                                                $batchAvailableClass = $batch->available_stock > 10 
                                                                    ? 'success' 
                                                                    : ($batch->available_stock > 0 ? 'warning' : 'danger');
                                                            @endphp
                                                            
                                                            <tr>
                                                                <td class="fw-medium">{{ $batch->batch_code }}</td>
                                                                <td class="text-center">{{ number_format($batch->total_purchase) }}</td>
                                                                <td class="text-center">{{ number_format($batch->total_sold) }}</td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-{{ $batchAvailableClass }} bg-opacity-10 text-{{ $batchAvailableClass }} px-3 py-1 fw-bold">
                                                                        {{ number_format($batch->available_stock) }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    {{ $expiryDate->format('d M Y') }}
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($isExpired)
                                                                        <span class="badge bg-danger">Expired</span>
                                                                    @elseif($isNearExpiry)
                                                                        <span class="badge bg-warning">
                                                                            Expires in {{ $daysUntilExpiry }} days
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-success">Valid</span>
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
                                    <div class="text-muted">
                                        <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                        <h5>No stock items found</h5>
                                        <p class="mb-0">Try adjusting your search or filter criteria</p>
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
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
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
            
            if (batchRow.style.display === 'none') {
                batchRow.style.display = 'table-row';
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-down');
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
            } else {
                batchRow.style.display = 'none';
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-right');
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-primary');
            }
        });
    });
    
    // Click on row to toggle (except when clicking on links or buttons)
    document.querySelectorAll('.stock-row').forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't toggle if clicking on a link or button
            if (e.target.closest('a') || e.target.closest('button')) {
                return;
            }
            
            const stockId = this.dataset.stockId;
            const toggleBtn = document.querySelector(`.toggle-batches[data-stock-id="${stockId}"]`);
            if (toggleBtn) {
                toggleBtn.click();
            }
        });
        
        // Change cursor to pointer
        row.style.cursor = 'pointer';
    });
});
</script>

{{-- Custom Styles --}}
<style>
.batch-container {
    background: linear-gradient(to bottom, #f8f9fa, #ffffff);
    border-top: 2px solid #dee2e6;
    animation: slideDown 0.3s ease-out;
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
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.batch-table thead th {
    background-color: #e9ecef;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #dee2e6;
}

.batch-table tbody tr {
    transition: background-color 0.2s ease;
}

.batch-table tbody tr:hover {
    background-color: #f8f9fa;
}

.table > :not(caption) > * > * {
    padding: 1rem 0.5rem;
}

.badge {
    font-weight: 500;
}

.toggle-batches {
    width: 20px;
    height: 20px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.toggle-batches i {
    font-size: 0.9rem;
    transition: transform 0.3s ease;
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

/* Custom scroll for table */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Link hover effect */
.table td a {
    transition: all 0.2s ease;
}

.table td a:hover {
    opacity: 0.8;
}

/* Empty state styling */
.text-muted i.bi-inbox {
    opacity: 0.5;
}

/* Batch count badge */
.badge i {
    font-size: 0.9rem;
}
</style>

{{-- Include Bootstrap Icons if not already in layout --}}
@if(!isset($hasBootstrapIcons))
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endif

@endsection