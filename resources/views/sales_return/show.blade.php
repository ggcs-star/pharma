@extends('layouts.app')

@section('content')

<div class="container py-4">
    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-1" style="color: #2c3e50;">
                <i class="fas fa-receipt me-2 text-danger"></i>Sales Return Details
            </h3>
            <p class="text-muted small mb-0">Complete information about this return transaction</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sales.return.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
            <button onclick="window.print()" class="btn btn-outline-primary rounded-pill px-3">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>

    <!-- Return Summary Card -->
    <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
        <div class="card-header py-3" style="background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-white fw-semibold">
                    <i class="fas fa-info-circle me-2"></i>Return Information
                </h6>
                <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                    <i class="fas fa-hashtag me-1"></i> {{ $return->return_number }}
                </span>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="d-flex align-items-center p-3 bg-light rounded-3">
                        <div class="bg-white rounded-circle p-2 me-3 shadow-sm">
                            <i class="fas fa-tag text-danger"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Return Number</small>
                            <span class="fw-bold">{{ $return->return_number }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center p-3 bg-light rounded-3">
                        <div class="bg-white rounded-circle p-2 me-3 shadow-sm">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Customer</small>
                            <span class="fw-bold">{{ $return->customer->name ?? 'Walk-in Customer' }}</span>
                            @if($return->customer->phone ?? false)
                                <small class="d-block text-muted">{{ $return->customer->phone }}</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center p-3 bg-light rounded-3">
                        <div class="bg-white rounded-circle p-2 me-3 shadow-sm">
                            <i class="fas fa-calendar-alt text-success"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Return Date</small>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($return->return_date)->format('d M Y') }}</span>
                            <small class="d-block text-muted">{{ \Carbon\Carbon::parse($return->return_date)->format('h:i A') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center p-3 bg-light rounded-3">
                        <div class="bg-white rounded-circle p-2 me-3 shadow-sm">
                            <i class="fas fa-rupee-sign text-warning"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Return Amount</small>
                            <span class="fw-bold fs-4 text-danger">₹ {{ number_format($return->net_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Original Sale Information Card - FIXED -->
    @if($return->sale)
    <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="mb-0 fw-semibold">
                <i class="fas fa-shopping-cart me-2 text-primary"></i>Original Sale Information
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="d-flex justify-content-between align-items-center py-3 px-3 bg-light rounded-3">
                        <span class="text-muted">
                            <i class="fas fa-receipt me-2"></i>Bill Number:
                        </span>
                        <span class="fw-semibold">{{ $return->sale->bill_number ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-between align-items-center py-3 px-3 bg-light rounded-3">
                        <span class="text-muted">
                            <i class="fas fa-calendar me-2"></i>Sale Date:
                        </span>
                        <span class="fw-semibold">
                            @if(isset($return->sale->sale_date))
                                {{ \Carbon\Carbon::parse($return->sale->sale_date)->format('d M Y') }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-between align-items-center py-3 px-3 bg-light rounded-3">
                        <span class="text-muted">
                            <i class="fas fa-rupee-sign me-2"></i>Original Amount:
                        </span>
                        <span class="fw-semibold text-primary">₹ {{ number_format($return->sale->total_amount ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Return Items Table -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="fas fa-boxes me-2 text-danger"></i>Returned Items
                </h6>
                <span class="badge bg-danger text-white rounded-pill px-3 py-2">
                    {{ $return->items->count() }} Item(s)
                </span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 ps-4" width="50">#</th>
                        <th class="py-3">ITEM DETAILS</th>
                        <th class="py-3 text-center" width="120">BATCH CODE</th>
                        <th class="py-3 text-center" width="100">RETURN QTY</th>
                        <th class="py-3 text-end" width="120">RATE (₹)</th>
                        <th class="py-3 text-end" width="150">AMOUNT (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($return->items as $index => $item)
                    <tr>
                        <td class="ps-4 fw-semibold text-muted">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">{{ $item->item->name ?? 'Unknown Item' }}</span>
                                @if($item->item->sku ?? false)
                                    <small class="text-muted">SKU: {{ $item->item->sku }}</small>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary bg-opacity-10 text-dark px-3 py-2 rounded-pill">
                                <i class="fas fa-cube me-1"></i>{{ $item->batch->batch_code ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold fs-5">{{ $item->qty_return }}</span>
                            <small class="text-muted d-block">units</small>
                        </td>
                        <td class="text-end fw-semibold">₹ {{ number_format($item->rate, 2) }}</td>
                        <td class="text-end fw-bold text-danger">₹ {{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <td colspan="5" class="text-end fw-bold py-3">Total Return Amount:</td>
                        <td class="text-end fw-bold fs-5 text-danger py-3">₹ {{ number_format($return->net_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Action Buttons Footer -->
    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
        <div class="text-muted small">
            <i class="fas fa-clock me-1"></i> Return processed on {{ \Carbon\Carbon::parse($return->created_at)->format('d M Y h:i A') }}
        </div>
        <div class="d-flex gap-2">
            @if($return->sale)
            <a href="{{ route('sales.show', $return->sale->id) }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-eye me-1"></i> View Original Sale
            </a>
            @endif
            <a href="{{ route('sales.return.index') }}" class="btn btn-danger rounded-pill px-4">
                <i class="fas fa-arrow-left me-1"></i> Back to Returns
            </a>
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
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
        background-color: #f8f9fa;
    }
    
    .table td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }
    
    .table tfoot td {
        font-size: 1rem;
        border-top: 2px solid #dee2e6;
    }
    
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    /* Print styles */
    @media print {
        .btn, .d-flex.gap-2, .d-flex.justify-content-between.align-items-center.mt-4 {
            display: none !important;
        }
        
        .container {
            width: 100% !important;
            max-width: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            break-inside: avoid;
        }
        
        .card-header {
            background: #f8f9fa !important;
            border-bottom: 1px solid #ddd !important;
        }
        
        .table {
            border: 1px solid #ddd;
        }
        
        .badge {
            border: 1px solid #ddd;
            background: #f8f9fa !important;
            color: #000 !important;
        }
        
        body {
            padding: 20px;
            font-size: 12pt;
        }
        
        .bg-light {
            background-color: #f8f9fa !important;
            print-color-adjust: exact;
        }
        
        .text-danger {
            color: #000 !important;
        }
    }
    
    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .card, .table {
        animation: fadeIn 0.5s ease-out;
    }
    
    /* Hover effects on info cards */
    .bg-light.rounded-3 {
        transition: all 0.3s ease;
    }
    
    .bg-light.rounded-3:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add print functionality enhancement
    const printBtn = document.querySelector('[onclick="window.print()"]');
    if (printBtn) {
        printBtn.addEventListener('click', function(e) {
            const originalTitle = document.title;
            document.title = 'Return_Details_{{ $return->return_number }}';
            
            setTimeout(function() {
                document.title = originalTitle;
            }, 1000);
        });
    }
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + P for print
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
        // Escape key to go back
        if (e.key === 'Escape') {
            window.history.back();
        }
    });
    
    // Animate amount value
    const amountElement = document.querySelector('.fs-4.text-danger');
    if (amountElement && parseFloat(amountElement.textContent.replace('₹ ', '')) > 0) {
        const finalAmount = {{ $return->net_amount }};
        let currentAmount = 0;
        const duration = 800;
        const stepTime = 20;
        const steps = duration / stepTime;
        const increment = finalAmount / steps;
        
        const counter = setInterval(function() {
            currentAmount += increment;
            if (currentAmount >= finalAmount) {
                currentAmount = finalAmount;
                clearInterval(counter);
            }
            amountElement.textContent = '₹ ' + currentAmount.toFixed(2);
        }, stepTime);
    }
});
</script>

@endsection