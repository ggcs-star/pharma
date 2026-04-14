@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-lg me-3"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-receipt fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">Order Details</h3>
                        <p class="text-muted mb-0">Order #{{ $order->id }} • {{ $order->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-light rounded-3 px-4 me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-primary rounded-3 px-4">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column - Order Info & Items --}}
        <div class="col-lg-8">
            {{-- ORDER INFO CARD --}}
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="fas fa-info-circle text-primary me-2"></i>Order Information
                    </h5>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small text-uppercase mb-2">Customer</label>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-circle fa-lg text-primary me-3"></i>
                                    <span class="fw-medium fs-5">{{ $order->user->name ?? 'Guest' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small text-uppercase mb-2">Total Amount</label>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-tag fa-lg text-success me-3"></i>
                                    <span class="fw-bold fs-4 text-success">₹{{ number_format($order->total ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small text-uppercase mb-2">Order Status</label>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-flag fa-lg text-warning me-3"></i>
                                    <span class="badge fs-6 px-4 py-2
                                        @if($order->status == 'pending') bg-warning text-dark
                                        @elseif($order->status == 'confirmed') bg-info text-white
                                        @elseif($order->status == 'processing') bg-primary text-white
                                        @elseif($order->status == 'shipped') bg-dark text-white
                                        @elseif($order->status == 'delivered') bg-success text-white
                                        @else bg-danger text-white
                                        @endif">
                                        {{ ucfirst($order->status ?? '-') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small text-uppercase mb-2">Payment Method</label>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-credit-card fa-lg text-info me-3"></i>
                                    @if($order->payment_mode == 'razorpay')
                                        <span class="badge bg-success text-white fs-6 px-4 py-2">
                                            <i class="fas fa-check-circle me-1"></i>Paid Online
                                        </span>
                                    @else
                                        <span class="badge bg-secondary text-white fs-6 px-4 py-2">
                                            <i class="fas fa-money-bill me-1"></i>Cash on Delivery
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- STATUS UPDATE FORM --}}
                    <div class="mt-4 pt-4 border-top">
                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                            @csrf
                            <label class="fw-semibold mb-3">
                                <i class="fas fa-sync-alt text-primary me-2"></i>Update Order Status
                            </label>
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <select name="status" class="form-select form-select-lg rounded-3" required>
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>✅ Confirmed</option>
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>🔄 Processing</option>
                                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>🚚 Shipped</option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>📦 Delivered</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-primary btn-lg w-100 rounded-3">
                                        <i class="fas fa-check me-2"></i>Update Status
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ORDER ITEMS CARD --}}
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="fas fa-shopping-cart text-primary me-2"></i>Order Items
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 py-3" style="border-radius: 8px 0 0 8px;">Product</th>
                                    <th class="py-3 text-center">Qty</th>
                                    <th class="py-3 text-end">Unit Price</th>
                                    <th class="py-3 text-end pe-3" style="border-radius: 0 8px 8px 0;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $index => $item)
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="item-icon me-3">
                                                <i class="fas fa-box text-primary"></i>
                                            </div>
                                            <span class="fw-medium">{{ $item->item->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="badge bg-light text-dark px-3 py-2">
                                            {{ $item->qty }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-end text-secondary">₹{{ number_format($item->price, 2) }}</td>
                                    <td class="py-3 text-end pe-3 fw-semibold text-success">₹{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="pt-3">
                                        <hr class="my-2">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="ps-3 py-3 text-end fw-bold fs-5">Grand Total:</td>
                                    <td class="py-3 text-end pe-3 fw-bold fs-5 text-success">₹{{ number_format($order->total ?? 0, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Address & Additional Info --}}
        <div class="col-lg-4">
            {{-- SHIPPING ADDRESS CARD --}}
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="fas fa-map-marker-alt text-danger me-2"></i>Shipping Address
                    </h5>
                    
                    @if($order->address)
                        <div class="address-card bg-light rounded-3 p-3">
                            <div class="d-flex mb-3">
                                <i class="fas fa-home fa-lg text-primary me-3 mt-1"></i>
                                <div>
                                    <p class="mb-1 fw-medium">{{ $order->address->address_line_1 }}</p>
                                    @if($order->address->address_line_2)
                                        <p class="mb-1 text-secondary">{{ $order->address->address_line_2 }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <i class="fas fa-city fa-lg text-info me-3 mt-1"></i>
                                <div>
                                    <p class="mb-0">{{ $order->address->city }}, {{ $order->address->state }}</p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <i class="fas fa-map-pin fa-lg text-success me-3 mt-1"></i>
                                <div>
                                    <p class="mb-0 fw-medium">{{ $order->address->pincode }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-map-marked-alt fa-3x text-muted mb-3 opacity-50"></i>
                            <p class="text-muted">No address provided</p>
                        </div>
                    @endif
                </div>
            </div>
{{-- PRESCRIPTION CARD --}}
@if($order->prescription)
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">
            <i class="fas fa-file-medical text-danger me-2"></i>Prescription
        </h5>

        <div class="text-center">
            <a href="{{ $order->prescription->file_path }}" target="_blank">
                <img src="{{ $order->prescription->file_path }}" 
                     class="img-fluid rounded-3 border"
                     style="max-height:250px;">
            </a>
        </div>

        <div class="mt-3">
            <span class="badge bg-warning text-dark">
                Status: {{ ucfirst($order->prescription->status ?? 'pending') }}
            </span>
        </div>
    </div>
</div>
@endif
            {{-- ORDER TIMELINE CARD --}}
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="fas fa-clock text-info me-2"></i>Order Timeline
                    </h5>
                    
                    <div class="timeline">
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-icon bg-success bg-opacity-10 p-2 rounded-3 me-3">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium">Order Placed</p>
                                    <small class="text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</small>
                                </div>
                            </div>
                        </div>
                        
                        @if($order->status != 'pending')
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-icon bg-info bg-opacity-10 p-2 rounded-3 me-3">
                                    <i class="fas fa-check-circle text-info"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium">Order Confirmed</p>
                                    <small class="text-muted">{{ $order->updated_at->format('d M Y, h:i A') }}</small>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if(in_array($order->status, ['processing', 'shipped', 'delivered']))
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-icon bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                                    <i class="fas fa-cog text-primary"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium">Processing</p>
                                    <small class="text-muted">Order is being processed</small>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if(in_array($order->status, ['shipped', 'delivered']))
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-icon bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                                    <i class="fas fa-truck text-warning"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium">Shipped</p>
                                    <small class="text-muted">Order is on the way</small>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($order->status == 'delivered')
                        <div class="timeline-item">
                            <div class="d-flex">
                                <div class="timeline-icon bg-success p-2 rounded-3 me-3">
                                    <i class="fas fa-check-double text-white"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium">Delivered</p>
                                    <small class="text-muted">Order completed successfully</small>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($order->status == 'cancelled')
                        <div class="timeline-item">
                            <div class="d-flex">
                                <div class="timeline-icon bg-danger p-2 rounded-3 me-3">
                                    <i class="fas fa-times text-white"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium">Cancelled</p>
                                    <small class="text-muted">Order was cancelled</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom CSS --}}
<style>
    /* General Styles */
    body {
        background: #f8f9fa;
    }
    
    /* Card Styles */
    .card {
        border: none;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
    }
    
    /* Info Item Styles */
    .info-item {
        padding: 12px;
        background: #f8f9fa;
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    
    .info-item:hover {
        background: #e9ecef;
        transform: translateY(-2px);
    }
    
    /* Table Styles */
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        color: #6c757d;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table td {
        border-bottom: 1px solid #f1f3f5;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background: #f8f9fa;
    }
    
    /* Item Icon */
    .item-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e7f1ff;
        border-radius: 10px;
    }
    
    /* Badge Styles */
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
        transition: all 0.2s ease;
    }
    
    /* Form Styles */
    .form-select-lg {
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .form-select-lg:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    
    /* Button Styles */
    .btn-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #0a58ca 0%, #084298 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
    }
    
    .btn-light {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }
    
    .btn-light:hover {
        background: #e9ecef;
        transform: translateY(-2px);
    }
    
    .btn-outline-primary:hover {
        transform: translateY(-2px);
    }
    
    /* Address Card */
    .address-card {
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }
    
    .address-card:hover {
        border-color: #0d6efd;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    /* Alert Styles */
    .alert-success {
        background: linear-gradient(135deg, #d1e7dd 0%, #b8d9ca 100%);
        border: none;
        color: #0a3622;
    }
    
    /* Timeline Styles */
    .timeline-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    
    .timeline-item {
        position: relative;
    }
    
    .timeline-item:not(:last-child):before {
        content: '';
        position: absolute;
        left: 19px;
        top: 40px;
        bottom: -15px;
        width: 2px;
        background: #dee2e6;
    }
    
    /* Print Styles */
    @media print {
        body {
            background: white;
        }
        
        .btn, form, .alert, nav, .btn-primary, .btn-light, .btn-outline-primary {
            display: none !important;
        }
        
        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
            break-inside: avoid;
        }
        
        .badge {
            border: 1px solid #000 !important;
            background: transparent !important;
            color: #000 !important;
        }
        
        .info-item {
            background: transparent !important;
            border: 1px solid #dee2e6;
        }
        
        .container-fluid {
            padding: 0 !important;
        }
    }
    
    /* Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem !important;
        }
        
        .fs-4 {
            font-size: 1.2rem !important;
        }
        
        .fs-5 {
            font-size: 1rem !important;
        }
        
        .btn-lg {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        .form-select-lg {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        .table {
            font-size: 0.85rem;
        }
        
        .item-icon {
            width: 30px;
            height: 30px;
        }
    }
    
    @media (max-width: 576px) {
        h3 {
            font-size: 1.3rem;
        }
        
        .badge {
            font-size: 0.75rem !important;
            padding: 0.25rem 0.5rem !important;
        }
        
        .info-item {
            padding: 8px;
        }
        
        .table th, .table td {
            padding: 0.5rem !important;
        }
    }
    
    /* Smooth Transitions */
    * {
        transition: all 0.2s ease-in-out;
    }
</style>
@endsection