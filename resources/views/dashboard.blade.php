@extends('layouts.master')

@section('content')
@if($expiredStockCount > 0 || $outOfStockCount > 0)

<div class="modal fade" id="stockAlertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    ⚠ Critical Stock Alert
                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>
            </div>

         <div class="modal-body">

    <h6 class="mb-3 text-danger">Expiring / Expired Medicines</h6>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Medicine</th>
                    <th>Batch</th>
                    <th>Expiry</th>
                    <th>Stock</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                {{-- Expiring Soon --}}
                @foreach($expiringSoonBatches as $batch)
                <tr>
                    <td>
   @if(!empty($batch->item->main_image))
    <img
        src="{{ $batch->item->main_image }}"
        width="50"
        height="50"
        style="object-fit: cover; border-radius: 8px;"
    >
@else
    <img
        src="{{ asset('images/default-medicine.png') }}"
        width="50"
        height="50"
        style="object-fit: cover; border-radius: 8px;"
    >
@endif
</td>

                    <td>{{ $batch->item->name ?? '-' }}</td>
<td>
    {{
        $batch->batch_code
        ?? $batch->batch_no
        ?? ('BATCH-ID-' . $batch->id)
    }}
</td>                    <td>{{ \Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') }}</td>
                    <td>{{ $batch->stock }}</td>

                    <td>
                        <span class="badge bg-warning text-dark">
                            Expiring Soon
                        </span>
                    </td>
                </tr>
                @endforeach

                {{-- Expired --}}
                @foreach($expiredBatches as $batch)
                <tr>
                   <td>
    @if(!empty($batch->item->main_image))
        <img
            src="{{ $batch->item->main_image }}"
            width="50"
            height="50"
            style="object-fit: cover; border-radius: 8px;"
        >
    @else
        <img
            src="{{ asset('images/default-medicine.png') }}"
            width="50"
            height="50"
            style="object-fit: cover; border-radius: 8px;"
        >
    @endif
</td>

                    <td>{{ $batch->item->name ?? '-' }}</td>
<td>
    {{
        $batch->batch_code
        ?? $batch->batch_no
        ?? ('BATCH-ID-' . $batch->id)
    }}
</td>                    <td>{{ \Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') }}</td>
                    <td>{{ $batch->stock }}</td>

                    <td>
                        <span class="badge bg-danger">
                            Expired
                        </span>
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

</div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

@endif
<div class="container-fluid px-4">
    {{-- Header with Welcome Message and Date --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-primary mb-1">Dashboard Overview</h4>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name ?? 'Admin' }}! Here's your pharmacy summary.</p>
        </div>
        <div class="text-end">
            <h6 class="mb-0 text-primary">{{ now()->format('l, d M Y') }}</h6>
            <small class="text-muted">Last updated: {{ now()->format('h:i A') }}</small>
        </div>
    </div>

    {{-- Quick Action Buttons with Route Checks --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="btn-group flex-wrap" role="group">
                @if(Route::has('sales.create'))
                <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='{{ route('sales.create') }}'">
                    <i class="fas fa-plus-circle me-1"></i>New Sale
                </button>
                @else
                <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='#'" disabled>
                    <i class="fas fa-plus-circle me-1"></i>New Sale
                </button>
                @endif

                @if(Route::has('purchases.create'))
                <button class="btn btn-outline-success btn-sm" onclick="window.location.href='{{ route('purchases.create') }}'">
                    <i class="fas fa-cart-plus me-1"></i>New Purchase
                </button>
                @else
                <button class="btn btn-outline-success btn-sm" onclick="window.location.href='#'" disabled>
                    <i class="fas fa-cart-plus me-1"></i>New Purchase
                </button>
                @endif

                @if(Route::has('stock.check'))
                <button class="btn btn-outline-warning btn-sm" onclick="window.location.href='{{ route('stock.check') }}'">
                    <i class="fas fa-exclamation-triangle me-1"></i>Check Stock
                </button>
                @else
                <button class="btn btn-outline-warning btn-sm" onclick="window.location.href='#'" disabled>
                    <i class="fas fa-exclamation-triangle me-1"></i>Check Stock
                </button>
                @endif

                @if(Route::has('reports.index'))
                <button class="btn btn-outline-info btn-sm" onclick="window.location.href='{{ route('reports.index') }}'">
                    <i class="fas fa-chart-bar me-1"></i>Generate Report
                </button>
                @else
                <button class="btn btn-outline-info btn-sm" onclick="window.location.href='#'" disabled>
                    <i class="fas fa-chart-bar me-1"></i>Generate Report
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ROW 1 - Financial Cards --}}
    <div class="row g-4 mb-4">
        {{-- Today Sales with Trend --}}
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card today-sales h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="icon-circle bg-soft-success">
                            <i class="fas fa-shopping-cart text-success"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-25 text-success">
                            <i class="fas fa-arrow-up me-1"></i>{{ $todaySalesTrend ?? '0' }}%
                        </span>
                    </div>
                    <h6 class="text-muted fw-normal mb-2">Today's Customer Purchases</h6>
                    <h3 class="fw-bold text-success mb-0">₹ {{ number_format($todaySales ?? 0, 2) }}</h3>
                    <small class="text-muted">{{ $todayPurchaseCount ?? 0 }} purchases today</small>
                </div>
            </div>
        </div>

        {{-- Monthly Sales --}}
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card monthly-sales h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="icon-circle bg-soft-primary">
                            <i class="fas fa-calendar-alt text-primary"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-25 text-primary">MTD</span>
                    </div>
                    <h6 class="text-muted fw-normal mb-2">Monthly Customer Purchases</h6>
                    <h3 class="fw-bold text-primary mb-0">₹ {{ number_format($monthlySales ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>

        {{-- Today Purchase --}}
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card today-purchase h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="icon-circle bg-soft-warning">
                            <i class="fas fa-truck text-warning"></i>
                        </div>
                        <span class="badge bg-warning bg-opacity-25 text-warning">Stock In</span>
                    </div>
                    <h6 class="text-muted fw-normal mb-2">Today's Supplier Purchases</h6>
                    <h3 class="fw-bold text-warning mb-0">₹ {{ number_format($todayPurchase ?? 0, 2) }}</h3>
                    <small class="text-muted">{{ $purchaseTransactions ?? 0 }} stock entries</small>
                </div>
            </div>
        </div>

        {{-- Net Profit/Loss --}}
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card net-profit h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="icon-circle bg-soft-info">
                            <i class="fas fa-chart-line text-info"></i>
                        </div>
                        @php
                            $netProfit = ($todaySales ?? 0) - ($todayPurchase ?? 0);
                            $profitClass = $netProfit >= 0 ? 'text-success' : 'text-danger';
                        @endphp
                        <span class="badge bg-info bg-opacity-25 text-info">Net</span>
                    </div>
                    <h6 class="text-muted fw-normal mb-2">Gross Profit Margin</h6>
                    <h3 class="fw-bold {{ $profitClass }} mb-0">₹ {{ number_format($netProfit, 2) }}</h3>
                    <small class="text-muted">Today's estimated margin</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2 - Outstanding & Alerts --}}
    <div class="row g-4 mb-4">
        {{-- Customer Outstanding with Progress --}}
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-soft-danger me-3">
                            <i class="fas fa-users text-danger"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">Customer Outstanding</h6>
                            <h4 class="fw-bold text-danger mb-0">₹ {{ number_format($customerOutstanding ?? 0, 2) }}</h4>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Overdue</small>
                            <small class="text-danger">{{ $overdueCustomers ?? 0 }} customers</small>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-danger" style="width: 65%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Supplier Payable --}}
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-soft-secondary me-3">
                            <i class="fas fa-hand-holding-usd text-secondary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">Supplier Payable</h6>
                            <h4 class="fw-bold text-secondary mb-0">₹ {{ number_format($supplierPayable ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stock Alerts Combined --}}
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted fw-normal mb-3">Stock Alerts</h6>
                    <div class="d-flex justify-content-around">
                        <div class="text-center">
                            <div class="position-relative d-inline-block">
                                <i class="fas fa-boxes fa-2x text-warning mb-2"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                    {{ $lowStockCount ?? 0 }}
                                </span>
                            </div>
                            <h6 class="mb-0 fw-bold">{{ $lowStockCount ?? 0 }}</h6>
                            <small class="text-muted">Low Stock</small>
                        </div>
                        <div class="text-center">
                            <div class="position-relative d-inline-block">
                                <i class="fas fa-calendar-times fa-2x text-danger mb-2"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $expiryNearCount ?? 0 }}
                                </span>
                            </div>
                            <h6 class="mb-0 fw-bold">{{ $expiryNearCount ?? 0 }}</h6>
                            <small class="text-muted">Expiring Soon</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <h6 class="text-muted fw-normal mb-3">Quick Stats</h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="fas fa-pills text-primary me-2"></i>Total Medicines</span>
                        <span class="fw-bold">{{ $totalMedicines ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="fas fa-users text-success me-2"></i>Total Customers</span>
                        <span class="fw-bold">{{ $totalCustomers ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="fas fa-truck text-warning me-2"></i>Total Suppliers</span>
                        <span class="fw-bold">{{ $totalSuppliers ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-prescription-bottle text-info me-2"></i>Categories</span>
                        <span class="fw-bold">{{ $totalCategories ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 3 - Charts and Recent Activity --}}
    <div class="row g-4">
        {{-- Sales Chart --}}
        <div class="col-xl-8">
            <div class="card dashboard-card">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-bold mb-0">Sales Overview (Last 7 Days)</h6>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="300"></canvas>
                </div>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="col-xl-4">
            <div class="card dashboard-card">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-bold mb-0">Recent Customer Purchases</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentTransactions ?? [] as $transaction)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">{{ $transaction->time ?? 'N/A' }}</small>
                                <span>{{ $transaction->customer ?? 'Walk-in Customer' }}</span>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold">₹ {{ number_format($transaction->amount ?? 0, 2) }}</span>
                                <span class="badge {{ ($transaction->status ?? 'Paid') == 'Paid' ? 'bg-success' : 'bg-warning' }} bg-opacity-10 text-{{ ($transaction->status ?? 'Paid') == 'Paid' ? 'success' : 'warning' }} d-block mt-1">
                                    {{ $transaction->status ?? 'Paid' }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="fas fa-receipt fa-2x mb-2"></i>
                            <p class="mb-0">No recent transactions</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .dashboard-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .icon-circle {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bg-soft-success {
        background-color: rgba(40, 167, 69, 0.1);
    }
    
    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    .bg-soft-warning {
        background-color: rgba(255, 193, 7, 0.1);
    }
    
    .bg-soft-danger {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .bg-soft-info {
        background-color: rgba(23, 162, 184, 0.1);
    }
    
    .bg-soft-secondary {
        background-color: rgba(108, 117, 125, 0.1);
    }
    
    .card {
        border-radius: 15px;
    }
    
    .btn-group .btn {
        border-radius: 20px !important;
        margin: 0 2px;
    }
    
    .progress {
        border-radius: 10px;
        background-color: #e9ecef;
    }
    
    .list-group-item {
        border-left: none;
        border-right: none;
        padding: 1rem 1.25rem;
    }
    
    .list-group-item:first-child {
        border-top: none;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
    
    @media (max-width: 768px) {
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .btn-group .btn {
            flex: 1;
        }
    }
</style>
@endpush
@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if(session('show_stock_alert') && ($expiredStockCount > 0 || $outOfStockCount > 0))
<script>
document.addEventListener("DOMContentLoaded", function () {
    let modalElement = document.getElementById('stockAlertModal');

    if (modalElement) {
        let stockModal = new bootstrap.Modal(modalElement);
        stockModal.show();
    }
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {

    const ctx = document.getElementById('salesChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!},
            datasets: [{
                label: 'Customer Purchases (₹)',
                data: {!! json_encode($chartData ?? [12000, 19000, 15000, 25000, 22000, 30000, 28000]) !!},
                borderColor: 'rgb(40, 167, 69)',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

});
</script>

@endpush