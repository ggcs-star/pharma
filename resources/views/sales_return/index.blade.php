@extends('layouts.master')

@section('content')

<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-1" style="color: #2c3e50;">
                <i class="fas fa-undo-alt me-2 text-danger"></i>Sales Returns
            </h4>
            <p class="text-muted small mb-0">Manage and track all product returns and refunds</p>
        </div>
        <a href="{{ route('sales.return.create') }}" class="btn btn-danger rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus-circle me-2"></i> New Return
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-white-50">Total Returns</small>
                            <h3 class="mb-0 fw-bold">{{ $returns->total() }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-exchange-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="card-body p-3 text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-white-50">Total Amount</small>
                            <h3 class="mb-0 fw-bold">₹ {{ number_format($returns->sum('net_amount'), 0) }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-rupee-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body p-3 text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-white-50">This Month</small>
                            <h3 class="mb-0 fw-bold">{{ $returns->whereBetween('return_date', [now()->startOfMonth(), now()->endOfMonth()])->count() }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body p-3 text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-white-50">Avg Return Value</small>
                            <h3 class="mb-0 fw-bold">₹ {{ number_format($returns->avg('net_amount') ?? 0, 0) }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-0 d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-list me-2 text-danger"></i>Return Records
                </h5>
            </div>
            <div class="d-flex gap-2 mt-2 mt-sm-0">
                <div class="input-group" style="width: 250px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Search returns...">
                </div>
                <button class="btn btn-outline-secondary" id="exportBtn">
                    <i class="fas fa-download me-1"></i> Export
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3 rounded-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i>
                        <span>{{ session('success') }}</span>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-3 rounded-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span>{{ session('error') }}</span>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="returnsTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4" width="60">#</th>
                            <th class="py-3">Return No</th>
                            <th class="py-3">Bill No</th>
                            <th class="py-3">Customer</th>
                            <th class="py-3">Return Amount</th>
                            <th class="py-3">Return Date</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center" width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse($returns as $i => $return)
                        <tr class="return-row" data-return-id="{{ $return->id }}" data-customer="{{ strtolower($return->customer->name ?? 'walk-in') }}" data-return-no="{{ $return->return_number ?? '' }}" data-bill-no="{{ strtolower($return->sale->bill_number ?? '') }}">
                            <td class="ps-4 fw-semibold text-muted">{{ $returns->firstItem() + $i }}</td>
                            <td>
                                <span class="fw-semibold">{{ $return->return_number ?? 'SR-' . str_pad($return->id, 6, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark px-3 py-2 rounded-pill">
                                    <i class="fas fa-receipt me-1"></i>{{ $return->sale->bill_number ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-2 me-2">
                                        <i class="fas fa-user text-muted fa-sm"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $return->customer->name ?? 'Walk-in Customer' }}</div>
                                        <small class="text-muted">{{ $return->customer->phone ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-danger">
                                    ₹ {{ number_format($return->net_amount, 2) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ date('d M Y', strtotime($return->return_date)) }}</span>
                                    <small class="text-muted">{{ date('h:i A', strtotime($return->return_date)) }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                    <i class="fas fa-check-circle me-1"></i> Completed
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('sales.return.show', $return->id) }}" 
                                   class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                   data-bs-toggle="tooltip"
                                   title="View Details">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="bg-light rounded-circle p-4 mb-3">
                                        <i class="fas fa-exchange-alt fa-3x text-muted"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">No Returns Found</h6>
                                    <p class="text-muted small mb-3">Start by creating a new sales return</p>
                                    <a href="{{ route('sales.return.create') }}" class="btn btn-danger btn-sm rounded-pill">
                                        <i class="fas fa-plus-circle me-1"></i> Create First Return
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($returns->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $returns->firstItem() }} to {{ $returns->lastItem() }} of {{ $returns->total() }} entries
                    </div>
                    <div>
                        {{ $returns->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .rounded-4 {
        border-radius: 1rem !important;
    }
    
    .rounded-3 {
        border-radius: 0.75rem !important;
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
    }
    
    .table td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }
    
    .table-hover tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateX(2px);
    }
    
    .btn-outline-primary {
        border-width: 1.5px;
    }
    
    .btn-outline-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(13, 110, 253, 0.2);
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    
    .stat-card {
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    /* Animation for alerts */
    .alert {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Search highlight */
    .highlight {
        background-color: #fff3cd !important;
        transition: background-color 0.3s ease;
    }
    
    /* Pagination styling */
    .pagination {
        margin-bottom: 0;
    }
    
    .page-link {
        border-radius: 0.5rem !important;
        margin: 0 2px;
        border: none;
        color: #6c757d;
        padding: 0.5rem 0.75rem;
    }
    
    .page-item.active .page-link {
        background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
        color: white;
    }
    
    .page-link:hover {
        background-color: #f8f9fa;
        color: #ff416c;
        transform: translateY(-1px);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('.return-row');
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;
            
            rows.forEach(row => {
                const returnNo = row.getAttribute('data-return-no') || '';
                const billNo = row.getAttribute('data-bill-no') || '';
                const customer = row.getAttribute('data-customer') || '';
                
                if (searchTerm === '') {
                    row.style.display = '';
                    row.classList.remove('highlight');
                    visibleCount++;
                } else if (returnNo.includes(searchTerm) || 
                           billNo.includes(searchTerm) || 
                           customer.includes(searchTerm)) {
                    row.style.display = '';
                    row.classList.add('highlight');
                    visibleCount++;
                    // Remove highlight after 1 second
                    setTimeout(() => {
                        row.classList.remove('highlight');
                    }, 1000);
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            const tableBody = document.getElementById('tableBody');
            let noResultsRow = document.getElementById('noResultsRow');
            
            if (visibleCount === 0 && searchTerm !== '') {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'noResultsRow';
                    noResultsRow.innerHTML = `
                        <td colspan="8" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No returns found matching "${searchTerm}"</p>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(noResultsRow);
                }
            } else if (noResultsRow) {
                noResultsRow.remove();
            }
        });
    }
    
    // Export functionality
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            exportTableToCSV('sales_returns_export.csv');
        });
    }
    
    function exportTableToCSV(filename) {
        const csv = [];
        const rows = document.querySelectorAll('#returnsTable tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = [];
            const cols = rows[i].querySelectorAll('td, th');
            
            for (let j = 0; j < cols.length; j++) {
                // Get text content, remove HTML tags and icons
                let text = cols[j].innerText.replace(/\n/g, ' ').trim();
                // Remove emojis and special characters if needed
                text = text.replace(/[^\x20-\x7E]/g, '');
                row.push('"' + text + '"');
            }
            
            csv.push(row.join(','));
        }
        
        const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
        const downloadLink = document.createElement('a');
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = 'none';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
        
        // Show success toast
        showToast('Export completed successfully!', 'success');
    }
    
    // Toast notification function
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `position-fixed bottom-0 end-0 m-3 alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show shadow-lg rounded-3`;
        toast.style.zIndex = '9999';
        toast.style.minWidth = '300px';
        toast.style.animation = 'slideUp 0.3s ease-out';
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'} me-2"></i>
                <span>${message}</span>
                <button type="button" class="btn-close ms-3" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
    
    // Add keyboard shortcut (Ctrl + F for search focus)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            if (searchInput) {
                searchInput.focus();
            }
        }
    });
    
    // Add animation to stat cards
    const statCards = document.querySelectorAll('.col-md-3 .card');
    statCards.forEach((card, index) => {
        card.style.animation = `slideIn 0.5s ease-out ${index * 0.1}s`;
        card.style.opacity = '0';
        card.style.animationFillMode = 'forwards';
    });
    
    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stat-card {
            animation: slideIn 0.5s ease-out;
        }
    `;
    document.head.appendChild(style);
    
    // Initialize stat cards opacity
    statCards.forEach(card => {
        card.style.opacity = '1';
    });
});

// Add this to ensure Bootstrap JS is loaded for alerts and tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Refresh tooltips for dynamically added elements
    const refreshTooltips = function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    };
    refreshTooltips();
});
</script>

@endsection