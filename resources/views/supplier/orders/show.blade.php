@extends('supplier.layouts.app')

@section('title', 'Order #' . ($order->order_number ?? 'Details'))

@section('content')
<div class="container-fluid px-0">
    <!-- Back Button & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <a href="{{ route('supplier.orders.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2">
                <i class="fas fa-arrow-left me-1"></i>Back to Orders
            </a>
            <h3 class="fw-bold text-dark mb-1 mt-2">
                <i class="fas fa-receipt text-primary me-2"></i>Order Details
            </h3>
            <p class="text-muted small mb-0">Order #{{ $order->order_number }}</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Print
            </button>
            <button class="btn btn-outline-primary btn-sm" onclick="window.location.reload()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Order Info Cards Row -->
    <div class="row g-3 mb-4">
        <!-- Order Summary Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <h6 class="text-muted mb-0"><i class="fas fa-info-circle me-1"></i>Order Information</h6>
                        <span class="badge 
                            @if($order->status == 'pending') bg-secondary
                            @elseif($order->status == 'confirmed') bg-success
                            @elseif($order->status == 'processing') bg-warning
                            @elseif($order->status == 'dispatched') bg-primary
                            @elseif($order->status == 'delivered') bg-dark
                            @elseif($order->status == 'rejected') bg-danger
                            @endif
                            px-3 py-2 rounded-pill">
                            <i class="fas 
                                @if($order->status == 'pending') fa-clock
                                @elseif($order->status == 'confirmed') fa-check-circle
                                @elseif($order->status == 'processing') fa-cogs
                                @elseif($order->status == 'dispatched') fa-truck
                                @elseif($order->status == 'delivered') fa-check-double
                                @elseif($order->status == 'rejected') fa-times-circle
                                @endif
                                me-1"></i>
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-muted small">Order Date</div>
                            <div class="fw-semibold">{{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : 'N/A' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Last Updated</div>
                            <div class="fw-semibold">{{ $order->updated_at ? $order->updated_at->format('d M Y, h:i A') : 'N/A' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small">Order Total</div>
                            <div class="fw-bold fs-4 text-primary">₹{{ number_format($order->net_amount, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Retailer Info Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h6 class="text-muted mb-3"><i class="fas fa-store me-1"></i>Retailer Information</h6>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-circle-large">
                            {{ strtoupper(substr($order->retailer->name ?? 'R', 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold fs-5">{{ $order->retailer->name ?? 'N/A' }}</div>
                            <div class="text-muted small">{{ $order->retailer->email ?? '' }}</div>
                        </div>
                    </div>
                    <div class="row g-2">
                        @if($order->retailer->phone ?? false)
                        <div class="col-12">
                            <i class="fas fa-phone-alt text-muted me-2" style="width: 20px;"></i>
                            <span>{{ $order->retailer->phone }}</span>
                        </div>
                        @endif
                        @if($order->shipping_address ?? false)
                        <div class="col-12">
                            <i class="fas fa-map-marker-alt text-muted me-2" style="width: 20px;"></i>
                            <span class="small">{{ $order->shipping_address }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Action Buttons Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="text-muted mb-3"><i class="fas fa-tasks me-1"></i>Update Order Status</h6>
            <form method="POST" action="{{ route('supplier.orders.status', $order->id) }}" class="d-flex flex-wrap gap-2 align-items-center">
                @csrf
                @if($order->status == 'pending')
                    <button name="status" value="confirmed" class="btn btn-success rounded-pill px-4">
                        <i class="fas fa-check-circle me-2"></i>Confirm Order
                    </button>
                    <button name="status" value="rejected" class="btn btn-danger rounded-pill px-4">
                        <i class="fas fa-times-circle me-2"></i>Reject Order
                    </button>
                @endif

                @if($order->status == 'confirmed')
                    <button name="status" value="processing" class="btn btn-warning rounded-pill px-4">
                        <i class="fas fa-cogs me-2"></i>Start Processing
                    </button>
                @endif

                @if($order->status == 'processing')
                    <button name="status" value="dispatched" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-truck me-2"></i>Mark as Dispatched
                    </button>
                @endif

               

                @if(in_array($order->status, ['pending', 'confirmed', 'processing', 'dispatched']))
                    <span class="text-muted small ms-2">
                        <i class="fas fa-info-circle me-1"></i>Update status as order progresses
                    </span>
                @endif
            </form>
        </div>
    </div>

    <!-- Order Items Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h6 class="fw-semibold mb-0"><i class="fas fa-boxes me-2"></i>Order Items</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Item</th>
                            <th class="py-3">Brand</th>
                            <th class="py-3">Batch No</th>
                            <th class="py-3">Expiry Date</th>
                            <th class="py-3 text-center">Stock</th>
                            <th class="py-3 text-end">Purchase Price</th>
                            <th class="py-3 text-end">Retailer Price</th>
                            <th class="py-3 text-end">MRP</th>
                            <th class="py-3 text-end">Base Price</th>
                            <th class="py-3 text-center">Qty</th>
                            <th class="py-3 text-center">GST</th>
                            <th class="py-3 text-end pe-4">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->items as $row)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $row->item->name ?? 'N/A' }}</div>
                                <small class="text-muted">ID: {{ $row->item->id ?? '' }}</small>
                            </td>
                            <td>{{ $row->item->brand ?? 'N/A' }}</td>
                            <td>
                                <span class="font-monospace small">{{ optional($row->catalog)->batch_no ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @php
                                    $expiry = optional($row->catalog)->expiry_date;
                                    $isExpired = $expiry && \Carbon\Carbon::parse($expiry)->isPast();
                                @endphp
                                <span class="{{ $isExpired ? 'text-danger fw-semibold' : '' }}">
                                    {{ $expiry ?? 'N/A' }}
                                    @if($isExpired) <i class="fas fa-exclamation-triangle ms-1"></i> @endif
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary bg-opacity-10 text-dark rounded-pill">
                                    {{ optional($row->catalog)->current_stock ?? 0 }}
                                </span>
                            </td>
                            <td class="text-end">₹{{ number_format(optional($row->catalog)->purchase_price ?? 0, 2) }}</td>
                            <td class="text-end">₹{{ number_format(optional($row->catalog)->retailer_price ?? 0, 2) }}</td>
                            <td class="text-end">₹{{ number_format(optional($row->catalog)->retailer_mrp ?? 0, 2) }}</td>
<td class="text-end fw-semibold">
    ₹{{ number_format(optional($row->catalog)->base_price ?? 0, 2) }}
</td>                            <td class="text-center">
                                <span class="fw-bold">{{ $row->quantity ?? 0 }}</span>
                            </td>
                            <td class="text-center">{{ $row->gst_percent ?? 0 }}%</td>
                            <td class="text-end pe-4 fw-bold text-primary">₹{{ number_format($row->total_amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-5">
                                <i class="fas fa-box-open fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No items found in this order</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="11" class="text-end fw-bold py-3">Subtotal:</td>
                            <td class="text-end pe-4 py-3 fw-bold">₹{{ number_format($order->items->sum('total_amount'), 2) }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr>
                            <td colspan="11" class="text-end py-2">Discount:</td>
                            <td class="text-end pe-4 text-danger">- ₹{{ number_format($order->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($order->tax_amount > 0)
                        <tr>
                            <td colspan="11" class="text-end py-2">Tax (GST):</td>
                            <td class="text-end pe-4">+ ₹{{ number_format($order->tax_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td colspan="11" class="text-end fw-bold fs-5 py-3">Grand Total:</td>
                            <td class="text-end pe-4 fw-bold fs-5 text-primary">₹{{ number_format($order->net_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Order Timeline / Notes Section (Optional) -->
    @if($order->notes || $order->timeline_logs)
    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-body p-4">
            <h6 class="text-muted mb-3"><i class="fas fa-history me-1"></i>Order History & Notes</h6>
            @if($order->notes)
                <div class="alert alert-light mb-3 rounded-3">
                    <i class="fas fa-sticky-note me-2 text-muted"></i>
                    {{ $order->notes }}
                </div>
            @endif
            <div class="small text-muted">
                <i class="fas fa-clock me-1"></i> Last updated: {{ $order->updated_at ? $order->updated_at->diffForHumans() : 'N/A' }}
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .avatar-circle-large {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.2rem;
    }
    
    .table > :not(caption) > * > * {
        border-bottom-color: #f0f2f5;
        vertical-align: middle;
    }
    
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }
    
    .badge.bg-secondary.bg-opacity-10 {
        background-color: rgba(108, 117, 125, 0.1) !important;
    }
    
    /* Print styles */
    @media print {
        .btn, .sidebar-toggle, .topbar, .sidebar, .user-section, form, .no-print {
            display: none !important;
        }
        .main-content-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        body {
            background: white;
        }
    }
</style>

@push('scripts')
<script>
    // Add confirmation before status change
    document.querySelectorAll('form button[value]').forEach(button => {
        button.addEventListener('click', function(e) {
            const status = this.value;
            const messages = {
                'confirmed': 'Confirm this order?',
                'rejected': 'Reject this order? This action cannot be undone.',
                'processing': 'Mark order as processing?',
                'dispatched': 'Mark order as dispatched?',
                'delivered': 'Mark order as delivered?'
            };
            
            if (messages[status] && !confirm(messages[status])) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
@endsection