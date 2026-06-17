@extends('layouts.master')

@section('content')
    <div class="container py-4">
        {{-- Header with Breadcrumb --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="mb-0 fw-semibold">
                    <i class="bi bi-box-seam me-2 text-primary"></i>{{ $item->name }}
                </h2>
                <p class="text-muted mb-0 mt-1">Item Details & Stock History</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stock.index') }}" class="text-decoration-none">Stock Management</a></li>
                    <li class="breadcrumb-item active text-truncate" style="max-width: 200px;">{{ $item->name }}</li>
                </ol>
            </nav>
        </div>

        {{-- Summary Cards --}}
        <div class="row g-4 mb-5">
            {{-- Total Purchased Card --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                    <i class="bi bi-cart-plus text-primary fs-3"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1 text-uppercase small fw-semibold">Total Purchased</h6>
                                <h3 class="mb-0 fw-bold">
                                    @if(($item->pack_qty ?? 1) > 1)
                                        {{ number_format($totalPurchased ?? 0) }} Strip
                                    @else
                                        {{ number_format($grandTotalSold ?? 0) }} {{ $item->product_form ?? 'Unit' }}
                                    @endif
                                </h3>
                                <small class="text-muted">units received</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Sold Card --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                    <i class="bi bi-cart-check text-success fs-3"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1 text-uppercase small fw-semibold">Total Sold</h6>
                                <h3 class="mb-0 fw-bold">
                                    @if(($item->pack_qty ?? 1) > 1)
                                        @php
                                            $finalSoldStrip = $finalSoldStrip ?? 0;
                                            $finalSoldLoose = $finalSoldLoose ?? 0;
                                        @endphp
                                        @if($finalSoldStrip > 0)
                                            {{ number_format($finalSoldStrip) }} Strip
                                        @endif
                                        @if($finalSoldLoose > 0)
                                            @if($finalSoldStrip > 0) + @endif
                                            {{ number_format($finalSoldLoose) }} Tablet
                                        @endif
                                        @if($finalSoldStrip <= 0 && $finalSoldLoose <= 0)
                                            0
                                        @endif
                                    @else
                                        {{ number_format($finalSoldLoose ?? 0) }} {{ $item->product_form ?? 'Unit' }}
                                    @endif
                                </h3>
                                <small class="text-muted">units sold</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Available Stock Card --}}
            <div class="col-md-4">
                @php
                    $availableTotal = $finalAvailableStrip ?? 0;
                    $stockClass = $availableTotal > 10 ? 'success' : ($availableTotal > 0 ? 'warning' : 'danger');
                    $stockIcon = $availableTotal > 10 ? 'check-circle-fill' : ($availableTotal > 0 ? 'exclamation-triangle-fill' : 'x-circle-fill');
                    $mainUnit = $item->product_form ?? $item->packaging_detail ?? 'Unit';
                @endphp
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-{{ $stockClass }} bg-opacity-10 rounded-3 p-3">
                                    <i class="bi bi-{{ $stockIcon }} text-{{ $stockClass }} fs-3"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1 text-uppercase small fw-semibold">Available Stock</h6>
                                <h3 class="mb-0 fw-bold text-{{ $stockClass }}">
                                    @if(($item->pack_qty ?? 1) > 1)
                                        {{ number_format($finalAvailableStrip ?? 0) }} Strip
                                        @if(($finalAvailableLoose ?? 0) > 0)
                                            + {{ number_format($finalAvailableLoose ?? 0) }} Tablet
                                        @endif
                                    @else
                                        {{ number_format($finalAvailableUnit ?? 0) }} {{ $item->product_form ?? 'Unit' }}
                                    @endif
                                </h3>
                                @if(!empty($item->packaging_detail))
                                    <small class="text-muted d-block mt-1">{{ $item->packaging_detail }}</small>
                                @endif
                                <small class="text-muted">units remaining</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Batch Details Section --}}
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-upc-scan me-2 text-primary"></i>Batch Details
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Batch Code</th>
                                <th>Expiry Date</th>
                                <th class="text-center">Purchased</th>
                                <th class="text-center">Offline Sold</th>
                                <th class="text-center">Online Sold</th>
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
                                    $packSize = max((int) ($item->pack_qty ?? 1), 1);
                                    $strip = (int) ($batch->stock ?? 0);
                                    $loose = (int) ($batch->loose_stock ?? 0);
                                    $totalLoose = ($strip * $packSize) + $loose;
                                    $finalStrip = intdiv($totalLoose, $packSize);
                                    $finalLoose = $totalLoose % $packSize;
                                    
                                    if ($totalLoose <= 0) {
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
                                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">
                                            {{ number_format($batch->total_purchase) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
                                            {{ number_format($batch->total_sale ?? 0) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                            {{ number_format($batch->total_online_sale ?? 0) }}
                                        </span>
                                    </td>
                                    <td class="text-center fw-semibold">
                                        @if(($item->pack_qty ?? 1) > 1)
                                            {{ number_format($finalStrip) }} Strip
                                            @if($finalLoose > 0)
                                                + {{ number_format($finalLoose) }} Tablet
                                            @endif
                                        @else
                                            {{ number_format($batch->available_stock) }} {{ $item->product_form ?? 'Unit' }}
                                        @endif
                                    </td>
                                    <td class="text-center pe-4">
                                        <span class="badge bg-{{ $badgeClass }} bg-opacity-10 text-{{ $badgeClass }} px-3 py-2 rounded-pill">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox display-4 d-block mb-2 opacity-50"></i>
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
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white p-0 border-bottom">
                <ul class="nav nav-tabs card-header-tabs" id="historyTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="purchase-tab" data-bs-toggle="tab" data-bs-target="#purchase"
                            type="button" role="tab">
                            <i class="bi bi-cart-plus me-1"></i>Purchase History
                            <span class="badge bg-primary ms-1 rounded-pill">{{ $purchases->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button"
                            role="tab">
                            <i class="bi bi-cart-check me-1"></i>Sales History
                            <span class="badge bg-success ms-1 rounded-pill">{{ $sales->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="online-orders-tab" data-bs-toggle="tab" data-bs-target="#onlineOrders"
                            type="button" role="tab">
                            <i class="bi bi-globe me-1"></i>Online Orders
                            <span class="badge bg-primary ms-1 rounded-pill">{{ $onlineOrders->count() }}</span>
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
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Date</th>
                                        <th>Bill No</th>
                                        <th>Supplier</th>
                                        <th>Batch</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">PTR</th>
                                        <th class="text-center">MRP</th>
                                        <th class="text-center">Purchase Amount</th>
                                        <th class="text-center pe-4">Net Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchases as $purchase)
                                        @php
                                            $netQty = ($purchase->quantity ?? 0) + ($purchase->free_quantity ?? 0) - ($purchase->returned_quantity ?? 0);
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-medium">{{ \Carbon\Carbon::parse($purchase->created_at)->format('d M Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($purchase->created_at)->timezone('Asia/Kolkata')->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                                    {{ $purchase->purchase_bill_number ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $purchase->supplier_name ?? '-' }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">
                                                    {{ $purchase->purchase_batch_code ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <strong>{{ number_format($purchase->quantity ?? 0) }}</strong>
                                                @if(!empty($purchase->packaging_detail))
                                                    <br><small class="text-primary fw-semibold">{{ $purchase->packaging_detail }}</small>
                                                @elseif(!empty($purchase->product_form))
                                                    <br><small class="text-primary fw-semibold">{{ $purchase->product_form }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center fw-medium">₹{{ number_format($purchase->ptr ?? 0, 2) }}</td>
                                            <td class="text-center fw-medium">₹{{ number_format($purchase->mrp ?? 0, 2) }}</td>
                                            <td class="text-center fw-semibold">₹{{ number_format($purchase->total_amount ?? 0, 2) }}</td>
                                            <td class="text-center pe-4 fw-bold">{{ number_format($netQty) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5 text-muted">
                                                <i class="bi bi-cart-x display-4 d-block mb-2 opacity-50"></i>
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
                                <thead class="bg-light">
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
                                                <div class="fw-medium">{{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($sale->created_at)->timezone('Asia/Kolkata')->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                                    {{ $sale->bill_number ?? ('#' . $sale->order_id) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $sale->customer_name ?? 'Walk-in Customer' }}</div>
                                                @if($sale->customer_mobile)<small class="d-block text-muted">{{ $sale->customer_mobile }}</small>@endif
                                                @if($sale->customer_email)<small class="d-block text-muted">{{ $sale->customer_email }}</small>@endif
                                                @if($sale->customer_address)<small class="d-block text-muted">{{ $sale->customer_address }}</small>@endif
                                                @if($sale->doctor_name)<small class="d-block text-info">Doctor: {{ $sale->doctor_name }}</small>@endif
                                                @if($sale->sale_type)
                                                    <small class="d-block text-primary fw-semibold">
                                                        {{ ucfirst($sale->sale_type) }} ({{ $sale->unit_qty ?? $sale->qty }} qty) @ ₹{{ number_format($sale->selling_price ?? 0, 2) }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">{{ $sale->batch_code }}</span>
                                            </td>
                                            <td class="text-center fw-semibold">{{ number_format($sale->qty) }}</td>
                                            <td class="text-center fw-semibold">₹{{ number_format($sale->net_amount ?? 0, 2) }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">{{ ucfirst($sale->payment_type ?? 'Cash') }}</span>
                                            </td>
                                            <td class="text-center pe-4">
                                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">{{ ucfirst($sale->status ?? 'Completed') }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">
                                                <i class="bi bi-bag-x display-4 d-block mb-2 opacity-50"></i>
                                                No sales history found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Online Orders Tab --}}
                    <div class="tab-pane fade" id="onlineOrders" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Date</th>
                                        <th>Order ID</th>
                                        <th>Customer Details</th>
                                        <th>Batch</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Payment</th>
                                        <th class="text-center pe-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($onlineOrders as $order)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-medium">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($order->created_at)->timezone('Asia/Kolkata')->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">#{{ $order->order_id }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $order->customer_name ?? 'Online Customer' }}</div>
                                                @if($order->customer_email)<small class="d-block text-muted">{{ $order->customer_email }}</small>@endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">{{ $order->batch_code }}</span>
                                            </td>
                                            <td class="text-center fw-semibold">{{ number_format($order->qty) }}</td>
                                            <td class="text-center fw-semibold">₹{{ number_format($order->total ?? 0, 2) }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">{{ ucfirst($order->payment_mode ?? 'Online') }}</span>
                                            </td>
                                            <td class="text-center pe-4">
                                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">{{ ucfirst($order->status ?? 'Completed') }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">
                                                <i class="bi bi-globe display-4 d-block mb-2 opacity-50"></i>
                                                No online orders found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Footer with Actions --}}
            <div class="card-footer bg-white py-3 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i>Back to Stock List
                    </a>
                    <button onclick="window.print();" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="bi bi-printer me-1"></i>Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for Tooltips --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    {{-- Custom Styles --}}
    <style>
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }
        .table > :not(caption) > * > * {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }
        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            margin-right: 0.25rem;
            transition: all 0.2s;
        }
        .nav-tabs .nav-link:hover {
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.04);
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
        .rounded-pill {
            border-radius: 50rem !important;
        }
        @media print {
            .btn, .breadcrumb, .nav-tabs, .card-footer {
                display: none !important;
            }
            .card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }
            .table {
                border-collapse: collapse !important;
            }
            .hover-lift:hover {
                transform: none !important;
            }
        }
        @media (max-width: 768px) {
            .card-body .d-flex {
                flex-direction: column;
                text-align: center;
            }
            .flex-shrink-0 {
                margin-bottom: 0.75rem;
            }
            .nav-tabs .nav-link {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
        }
    </style>

    {{-- Include Bootstrap Icons if not already in layout --}}
    @if(!isset($hasBootstrapIcons))
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endif
@endsection