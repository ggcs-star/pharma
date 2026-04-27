@extends('layouts.master')

@section('content')
    <div class="container py-4">

        {{-- Header with Breadcrumb --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">
                    <i class="bi bi-box me-2"></i>{{ $item->name }}
                </h2>
                <p class="text-muted mb-0 mt-1">Item Details & Stock History</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stock.index') }}">Stock Management</a></li>
                    <li class="breadcrumb-item active">{{ $item->name }}</li>
                </ol>
            </nav>
        </div>

        {{-- Summary Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-primary h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-cart-plus text-primary fs-3"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Total Purchased</h6>
                                <h3 class="mb-0">{{ number_format($totalPurchase) }}</h3>
                                <small class="text-muted">units</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-success h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-cart-check text-success fs-3"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Total Sold</h6>
                                <h3 class="mb-0">{{ number_format($totalSold) }}</h3>
                                <small class="text-muted">units</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                @php
                    $stockClass = $available > 10 ? 'success' : ($available > 0 ? 'warning' : 'danger');
                    $stockIcon = $available > 10 ? 'check-circle' : ($available > 0 ? 'exclamation-circle' : 'x-circle');
                @endphp
                <div class="card border-{{ $stockClass }} h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-{{ $stockClass }} bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-{{ $stockIcon }} text-{{ $stockClass }} fs-3"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Available Stock</h6>
                                <h3 class="mb-0 text-{{ $stockClass }}">{{ number_format($available) }}</h3>
                                <small class="text-muted">units remaining</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Batch Details Section --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-upc-scan me-2"></i>Batch Details
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Batch Code</th>
                                <th>Expiry Date</th>
                                <th class="text-center">Purchased</th>
                                <th class="text-center">Sold</th>
                                <th class="text-center">Available</th>
                                <th class="text-center pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batches as $batch)
                                @php
                                    $expiryDate = \Carbon\Carbon::parse($batch->expiry_date);
                                    $isExpired = $expiryDate->isPast();
                                    $isNearExpiry = $expiryDate->diffInDays(now()) <= 30 && !$isExpired;
                                    $availableStock = $batch->available_stock;

                                    if ($availableStock == 0) {
                                        $badgeClass = 'secondary';
                                        $statusText = 'Out of Stock';
                                    } elseif ($isExpired) {
                                        $badgeClass = 'danger';
                                        $statusText = 'Expired';
                                    } elseif ($isNearExpiry) {
                                        $badgeClass = 'warning';
                                        $statusText = 'Near Expiry';
                                    } else {
                                        $badgeClass = 'success';
                                        $statusText = 'Active';
                                    }
                                @endphp
                                <tr>
                                    <td class="ps-4 fw-medium">{{ $batch->batch_code }}</td>
                                    <td>
                                        {{ $expiryDate->format('M Y') }}
                                        @if($isExpired)
                                            <i class="bi bi-exclamation-triangle-fill text-danger ms-2" data-bs-toggle="tooltip"
                                                title="Expired on {{ $expiryDate->format('d M Y') }}"></i>
                                        @elseif($isNearExpiry)
                                            <i class="bi bi-clock-fill text-warning ms-2" data-bs-toggle="tooltip"
                                                title="Expires in {{ $expiryDate->diffInDays(now()) }} days"></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                            {{ number_format($batch->total_purchase) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">
                                            {{ number_format($batch->total_sale) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold">{{ number_format($availableStock) }}</span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <span class="badge bg-{{ $badgeClass }} bg-opacity-10 text-{{ $badgeClass }} px-3 py-2">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                        No batch information available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Purchase & Sales History Tabs --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <ul class="nav nav-tabs card-header-tabs" id="historyTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="purchase-tab" data-bs-toggle="tab" data-bs-target="#purchase"
                            type="button" role="tab">
                            <i class="bi bi-cart-plus me-1"></i>Purchase History
                            <span class="badge bg-primary ms-1">{{ $purchases->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button"
                            role="tab">
                            <i class="bi bi-cart-check me-1"></i>Sales History
                            <span class="badge bg-success ms-1">{{ $sales->count() }}</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content" id="historyTabsContent">
                    {{-- Purchase History Tab --}}
                    <div class="tab-pane fade show active" id="purchase" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Date</th>
                                        <th>Batch Code</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Free Quantity</th>
                                        <th class="text-center">Returned</th>
                                        <th class="text-center pe-4">Net Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchases as $purchase)
                                        @php
                                            $netQty = $purchase->quantity + $purchase->free_quantity - $purchase->returned_quantity;
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-medium">
                                                    {{ \Carbon\Carbon::parse($purchase->created_at)->format('d M Y') }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($purchase->created_at)->format('h:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                    {{ $purchase->batch_code }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ number_format($purchase->quantity) }}</td>
                                            <td class="text-center">
                                                @if($purchase->free_quantity > 0)
                                                    <span class="badge bg-success bg-opacity-10 text-success">
                                                        +{{ number_format($purchase->free_quantity) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($purchase->returned_quantity > 0)
                                                    <span class="badge bg-danger bg-opacity-10 text-danger">
                                                        -{{ number_format($purchase->returned_quantity) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center pe-4 fw-medium">
                                                {{ number_format($netQty) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="bi bi-cart-x display-4 d-block mb-2"></i>
                                                No purchase history found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Sales History Tab --}}
                  <div class="tab-pane fade" id="sales" role="tabpanel">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Date</th>
                    <th>Bill</th>
                    <th>Customer Details</th>
                    <th>Batch</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Amount</th>
                    <th class="text-center">Payment</th>
                    <th class="text-center pe-4">Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($sales as $sale)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-medium">
                                {{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y') }}
                            </div>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($sale->created_at)->format('h:i A') }}
                            </small>
                        </td>

                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                {{ $sale->bill_number ?? ('#' . $sale->order_id) }}
                            </span>
                        </td>

                        <td>
                            <div class="fw-semibold">
                                {{ $sale->customer_name ?? 'Walk-in Customer' }}
                            </div>

                            @if($sale->customer_mobile)
                                <small class="d-block text-muted">
                                    {{ $sale->customer_mobile }}
                                </small>
                            @endif

                            @if($sale->customer_email)
                                <small class="d-block text-muted">
                                    {{ $sale->customer_email }}
                                </small>
                            @endif

                            @if($sale->customer_address)
                                <small class="d-block text-muted">
                                    {{ $sale->customer_address }}
                                </small>
                            @endif

                            @if($sale->doctor_name)
                                <small class="d-block text-info">
                                    Doctor: {{ $sale->doctor_name }}
                                </small>
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                {{ $sale->batch_code }}
                            </span>
                        </td>

                        <td class="text-center">
                            <strong>{{ number_format($sale->qty) }}</strong>
                        </td>

                        <td class="text-center">
                            ₹{{ number_format($sale->net_amount ?? 0, 2) }}
                        </td>

                        <td class="text-center">
                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                {{ ucfirst($sale->payment_type ?? 'Cash') }}
                            </span>
                        </td>

                        <td class="text-center pe-4">
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                {{ ucfirst($sale->status ?? 'Completed') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-bag-x display-4 d-block mb-2"></i>
                            No sales history found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
            {{-- Footer with Actions --}}
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Stock List
                    </a>

                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for Tooltips and Functions --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Export function (placeholder)
        function exportData() {
            alert('Export functionality will be implemented here');
            // You can implement CSV/PDF export here
        }
    </script>

    {{-- Custom Styles --}}
    <style>
        .card {
            border: none;
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .table> :not(caption)>*>* {
            padding: 1rem 0.75rem;
        }

        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }

        .nav-tabs .nav-link:hover {
            color: #0d6efd;
            border: none;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            background-color: transparent;
            border-bottom: 2px solid #0d6efd;
        }

        .badge {
            font-weight: 500;
        }

        .bg-opacity-10 {
            --bs-bg-opacity: 0.1;
        }

        /* Print styles */
        @media print {

            .btn,
            .breadcrumb,
            .nav-tabs {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }

            .table {
                border-collapse: collapse !important;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-body .d-flex {
                flex-direction: column;
                text-align: center;
            }

            .flex-shrink-0 {
                margin-bottom: 0.5rem;
            }
        }
    </style>

    {{-- Include Bootstrap Icons if not already in layout --}}
    @if(!isset($hasBootstrapIcons))
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endif

@endsection