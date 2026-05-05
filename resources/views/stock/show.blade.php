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


<h3 class="mb-0">
@if(in_array(strtolower($item->product_form), ['tablet','capsule']))

    {{ $totalPurchased ?? 0 }} Strip

@else

    {{ $totalPurchased ?? 0 }} {{ $item->product_form ?? 'Unit' }}

@endif
</h3>                              <small class="text-muted">units</small>
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

<h3 class="mb-0">
@if(in_array(strtolower($item->product_form), ['tablet','capsule']))

    {{ $finalSoldStrip ?? 0 }} Strip

    @if(($finalSoldLoose ?? 0) > 0)
        + {{ $finalSoldLoose }} Tablet
    @endif

@else

    {{ $finalSoldStrip ?? 0 }} {{ $item->product_form ?? 'Unit' }}

@endif
</h3> <small class="text-muted">units</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
       @php
$availableTotal = $finalAvailableStrip ?? 0;
    $stockClass = $availableTotal > 10
        ? 'success'
        : ($availableTotal > 0 ? 'warning' : 'danger');

    $stockIcon = $availableTotal > 10
        ? 'check-circle'
        : ($availableTotal > 0 ? 'exclamation-circle' : 'x-circle');

    /*
    |--------------------------------------------------------------------------
    | Dynamic Unit from DB
    |--------------------------------------------------------------------------
    */

    $mainUnit = $item->product_form
        ?? $item->packaging_detail
        ?? 'Unit';
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
                           @php
@endphp

<h3 class="mb-0 text-{{ $stockClass }}">
@if(in_array(strtolower($item->product_form), ['tablet','capsule']))

    {{ $finalAvailableStrip }} Strip

    @if($finalAvailableLoose > 0)
        + {{ $finalAvailableLoose }} Tablet
    @endif

@else

    {{ $finalAvailableUnit }} {{ $item->product_form ?? 'Unit' }}

@endif
</h3>
@if(!empty($item->packaging_detail))
    <small class="text-muted d-block mt-1">
        {{ $item->packaging_detail }}
    </small>
@endif
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
<th class="text-center">Offline Sold</th>
<th class="text-center">Online Sold</th>                                <th class="text-center">Available</th>
                                <th class="text-center pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batches as $batch)
                                @php
                                    $expiryDate = \Carbon\Carbon::parse($batch->expiry_date);
                                    $isExpired = $expiryDate->isPast();
                                    $isNearExpiry = $expiryDate->diffInDays(now()) <= 30 && !$isExpired;
$packSize = $item->pack_qty ?? 10;

$strip = (int) ($batch->stock ?? 0);
$loose = (int) ($batch->loose_stock ?? 0);

$availableStock = ($strip * $packSize) + $loose;if ($availableStock <= 0) {
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
        {{ number_format($batch->total_sale ?? 0) }}
    </span>
</td>

<td class="text-center">
    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
        {{ number_format($batch->total_online_sale ?? 0) }}
    </span>
</td>
                                    <td class="text-center">
<span class="fw-bold">
@php
    $packSize = $item->pack_qty ?? 10;

    $strip = (int) ($batch->stock ?? 0);
    $loose = (int) ($batch->loose_stock ?? 0);

    $totalLoose = ($strip * $packSize) + $loose;

    $finalStrip = intdiv($totalLoose, $packSize);
    $finalLoose = $totalLoose % $packSize;
@endphp

@if(in_array(strtolower($item->product_form), ['tablet','capsule']))

    {{ $finalStrip }} Strip

    @if($finalLoose > 0)
        + {{ $finalLoose }} Tablet
    @endif

@else

    {{ $batch->available_stock }} {{ $item->product_form ?? 'Unit' }}

@endif
</span>                                    </td>
                                    <td class="text-center pe-4">
                                        <span class="badge bg-{{ $badgeClass }} bg-opacity-10 text-{{ $badgeClass }} px-3 py-2">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
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
                    <li class="nav-item" role="presentation">
    <button class="nav-link"
            id="online-orders-tab"
            data-bs-toggle="tab"
            data-bs-target="#onlineOrders"
            type="button"
            role="tab">

        <i class="bi bi-globe me-1"></i>
        Online Orders

        <span class="badge bg-primary ms-1">
            {{ $onlineOrders->count() }}
        </span>
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
        $netQty = ($purchase->quantity ?? 0)
                + ($purchase->free_quantity ?? 0)
                - ($purchase->returned_quantity ?? 0);
    @endphp

    <tr>
        {{-- Date --}}
        <td class="ps-4">
            <div class="fw-medium">
                {{ \Carbon\Carbon::parse($purchase->created_at)->format('d M Y') }}
            </div>
            <small class="text-muted">
                {{ \Carbon\Carbon::parse($purchase->created_at)
                    ->timezone('Asia/Kolkata')
                    ->format('h:i A') }}
            </small>
        </td>

        {{-- Bill No --}}
        <td>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                {{ $purchase->purchase_bill_number ?? '-' }}
            </span>
        </td>

        {{-- Supplier --}}
        <td>
            <div class="fw-semibold">
                {{ $purchase->supplier_name ?? '-' }}
            </div>
        </td>

        {{-- Batch --}}
        <td>
            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                {{ $purchase->purchase_batch_code ?? '-' }}
            </span>
        </td>

        {{-- Qty --}}
<td class="text-center">
    <strong>{{ number_format($purchase->quantity ?? 0) }}</strong>

    @if(!empty($purchase->packaging_detail))
        <br>
        <small class="text-primary fw-semibold">
            {{ $purchase->packaging_detail }}
        </small>
    @elseif(!empty($purchase->product_form))
        <br>
        <small class="text-primary fw-semibold">
            {{ $purchase->product_form }}
        </small>
    @endif
</td>

        {{-- PTR --}}
        <td class="text-center">
            ₹{{ number_format($purchase->ptr ?? 0, 2) }}
        </td>

        {{-- MRP --}}
        <td class="text-center">
            ₹{{ number_format($purchase->mrp ?? 0, 2) }}
        </td>

        {{-- Purchase Amount --}}
        <td class="text-center">
            ₹{{ number_format($purchase->total_amount ?? 0, 2) }}
        </td>

        {{-- Net Qty --}}
        <td class="text-center pe-4 fw-bold">
            {{ number_format($netQty) }}
        </td>
    </tr>
@empty                                        <tr>
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
{{ \Carbon\Carbon::parse($sale->created_at)
    ->timezone('Asia/Kolkata')
    ->format('h:i A') }}                            </small>
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

    @if($sale->sale_type)
        <small class="d-block text-primary fw-semibold">
            {{ ucfirst($sale->sale_type) }}
            ({{ $sale->unit_qty ?? $sale->qty }} qty)
            @ ₹{{ number_format($sale->selling_price ?? 0, 2) }}
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
</div>{{-- Online Orders Tab --}}
<div class="tab-pane fade" id="onlineOrders" role="tabpanel">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">

            <thead class="table-light">
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
                            <div class="fw-medium">
                                {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}
                            </div>
                            <small class="text-muted">
{{ \Carbon\Carbon::parse($order->created_at)
    ->timezone('Asia/Kolkata')
    ->format('h:i A') }}                            </small>
                        </td>

                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                #{{ $order->order_id }}
                            </span>
                        </td>

                        <td>
                            <div class="fw-semibold">
                                {{ $order->customer_name ?? 'Online Customer' }}
                            </div>

                            @if($order->customer_email)
                                <small class="d-block text-muted">
                                    {{ $order->customer_email }}
                                </small>
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                {{ $order->batch_code }}
                            </span>
                        </td>

                        <td class="text-center">
                            <strong>{{ number_format($order->qty) }}</strong>
                        </td>

                        <td class="text-center">
                            ₹{{ number_format($order->total ?? 0, 2) }}
                        </td>

                        <td class="text-center">
                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                {{ ucfirst($order->payment_mode ?? 'Online') }}
                            </span>
                        </td>

                        <td class="text-center pe-4">
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                {{ ucfirst($order->status ?? 'Completed') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-globe display-4 d-block mb-2"></i>
                            No online orders found
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