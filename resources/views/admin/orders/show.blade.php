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

    {{-- ERROR MESSAGE --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fa-lg me-3"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
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
                            <div class="info-item p-3 bg-light rounded-3">
                                <label class="text-muted small text-uppercase mb-2 d-block">Customer</label>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-circle fa-lg text-primary me-3"></i>
                                    <span class="fw-medium fs-5">{{ $order->user->name ?? 'Guest User' }}</span>
                                </div>
                                <div class="mt-2 ps-4">
                                    <small class="text-muted">{{ $order->user->email ?? 'No email available' }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-item p-3 bg-light rounded-3">
                                <label class="text-muted small text-uppercase mb-2 d-block">Total Amount</label>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-tag fa-lg text-success me-3"></i>
                                    <span class="fw-bold fs-3 text-success">₹{{ number_format($order->total ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-item p-3 bg-light rounded-3">
                                <label class="text-muted small text-uppercase mb-2 d-block">Order Status</label>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-flag fa-lg text-warning me-3"></i>
                                    <span class="badge fs-6 px-4 py-2 rounded-pill
                                        @if($order->status == 'pending') bg-warning text-dark
                                        @elseif($order->status == 'confirmed') bg-info text-white
                                        @elseif($order->status == 'processing') bg-primary text-white
                                        @elseif($order->status == 'shipped') bg-dark text-white
                                        @elseif($order->status == 'delivered') bg-success text-white
                                        @else bg-danger text-white
                                        @endif">
                                        <i class="fas 
                                            @if($order->status == 'pending') fa-clock
                                            @elseif($order->status == 'confirmed') fa-check-circle
                                            @elseif($order->status == 'processing') fa-cog fa-spin
                                            @elseif($order->status == 'shipped') fa-truck
                                            @elseif($order->status == 'delivered') fa-check-double
                                            @else fa-times-circle
                                            @endif me-1"></i>
                                        {{ ucfirst($order->status ?? '-') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-item p-3 bg-light rounded-3">
                                <label class="text-muted small text-uppercase mb-2 d-block">Payment Method</label>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-credit-card fa-lg text-info me-3"></i>
                                    @if($order->payment_mode == 'razorpay')
                                        <span class="badge bg-success text-white fs-6 px-4 py-2 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> Paid Online
                                        </span>
                                        @if(isset($order->payment_status))
                                            <span class="ms-2 small text-muted">({{ ucfirst($order->payment_status) }})</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary text-white fs-6 px-4 py-2 rounded-pill">
                                            <i class="fas fa-money-bill me-1"></i> Cash on Delivery
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- STATUS UPDATE FORM --}}
                    <div class="mt-4 pt-4 border-top">
                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="row g-3 align-items-end">
                            @csrf
                            @method('PUT')
                            <div class="col-md-8">
                                <label class="fw-semibold mb-2 d-block">
                                    <i class="fas fa-sync-alt text-primary me-2"></i>Update Order Status
                                </label>
                                <select name="status" class="form-select form-select-lg rounded-3 border-2" required>
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>✅ Confirmed</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>🔄 Processing</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>🚚 Shipped</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>📦 Delivered</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-lg w-100 rounded-3" type="submit">
                                    <i class="fas fa-check me-2"></i>Update Status
                                </button>
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
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 py-3" style="border-radius: 8px 0 0 8px;">Product</th>
                                    <th class="py-3 text-center">Quantity</th>
                                    <th class="py-3 text-end">Unit Price</th>
                                    <th class="py-3 text-end pe-3" style="border-radius: 0 8px 8px 0;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->items as $item)
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="item-icon me-3">
                                                <i class="fas fa-box text-primary"></i>
                                            </div>
                                            <div>
                                                <span class="fw-medium">{{ $item->item->name ?? 'Product N/A' }}</span>
                                                @if(isset($item->item->sku))
                                                    <br><small class="text-muted">SKU: {{ $item->item->sku }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                            <i class="fas fa-times me-1"></i>{{ $item->qty }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-end text-secondary">₹{{ number_format($item->price, 2) }}</td>
                                    <td class="py-3 text-end pe-3 fw-semibold text-success">₹{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="fas fa-box-open fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted mb-0">No items found for this order</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <td colspan="3" class="ps-3 py-3 text-end fw-bold fs-5">Grand Total:</td>
                                    <td class="py-3 text-end pe-3 fw-bold fs-4 text-success">₹{{ number_format($order->total ?? 0, 2) }}</td>
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
                                    <p class="mb-1 fw-medium">{{ $order->address->address_line_1 ?? 'N/A' }}</p>
                                    @if(!empty($order->address->address_line_2))
                                        <p class="mb-1 text-secondary">{{ $order->address->address_line_2 }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <i class="fas fa-city fa-lg text-info me-3 mt-1"></i>
                                <div>
                                    <p class="mb-0">{{ $order->address->city ?? 'N/A' }}, {{ $order->address->state ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <i class="fas fa-map-pin fa-lg text-success me-3 mt-1"></i>
                                <div>
                                    <p class="mb-0 fw-medium">{{ $order->address->pincode ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @if(!empty($order->address->phone))
                            <div class="d-flex mt-3">
                                <i class="fas fa-phone fa-lg text-warning me-3 mt-1"></i>
                                <div>
                                    <p class="mb-0">{{ $order->address->phone }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-map-marked-alt fa-3x text-muted mb-3 opacity-50"></i>
                            <p class="text-muted mb-0">No shipping address provided</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- PRESCRIPTION CARD --}}
            @if(isset($order->prescription) && $order->prescription)
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-file-medical text-danger me-2"></i>Prescription
                    </h5>

                    <div class="text-center">
                        <a href="{{ asset($order->prescription->file_path) }}" target="_blank" class="d-block">
                            <img src="{{ asset($order->prescription->file_path) }}" 
                                 alt="Prescription"
                                 class="img-fluid rounded-3 border shadow-sm"
                                 style="max-height:200px; width: auto; cursor: pointer;">
                        </a>
                        <small class="text-muted mt-2 d-block">Click image to view full size</small>
                    </div>

                    <div class="mt-3 text-center">
                        <span class="badge 
                            @if(isset($order->prescription->status))
                                @if($order->prescription->status == 'approved') bg-success
                                @elseif($order->prescription->status == 'rejected') bg-danger
                                @else bg-warning text-dark
                                @endif
                            @else bg-warning text-dark
                            @endif px-4 py-2 rounded-pill">
                            <i class="fas 
                                @if(isset($order->prescription->status))
                                    @if($order->prescription->status == 'approved') fa-check-circle
                                    @elseif($order->prescription->status == 'rejected') fa-times-circle
                                    @else fa-clock
                                    @endif
                                @else fa-clock
                                @endif me-1"></i>
                            Status: {{ ucfirst($order->prescription->status ?? 'pending') }}
                        </span>
                    </div>
                    
                    @if(isset($order->prescription->notes) && $order->prescription->notes)
                        <div class="mt-3 p-2 bg-light rounded-3">
                            <small class="text-muted">Notes: {{ $order->prescription->notes }}</small>
                        </div>
                    @endif
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
                        {{-- Order Placed --}}
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-icon bg-success bg-opacity-10 p-2 rounded-3 me-3">
                                    <i class="fas fa-shopping-cart text-success"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium">Order Placed</p>
                                    <small class="text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</small>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Payment Confirmed --}}
                        @if($order->payment_mode == 'razorpay' && $order->payment_status == 'paid')
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-icon bg-info bg-opacity-10 p-2 rounded-3 me-3">
                                    <i class="fas fa-credit-card text-info"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium">Payment Confirmed</p>
                                    <small class="text-muted">Payment received successfully</small>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Order Confirmed --}}
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
                        
                        {{-- Processing --}}
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
                        
                        {{-- Shipped --}}
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
                        
                        {{-- Delivered --}}
                        @if($order->status == 'delivered')
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-icon bg-success bg-opacity-10 p-2 rounded-3 me-3">
                                    <i class="fas fa-check-double text-success"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium">Delivered</p>
                                    <small class="text-muted">Order completed successfully</small>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Cancelled --}}
                        @if($order->status == 'cancelled')
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-icon bg-danger bg-opacity-10 p-2 rounded-3 me-3">
                                    <i class="fas fa-times-circle text-danger"></i>
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
    .bg-opacity-10 {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    
    /* Card Styles */
    .card {
        border: none;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
    }
    
    /* Info Item Styles */
    .info-item {
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    
    .info-item:hover {
        border-color: #dee2e6;
        transform: translateY(-2px);
    }
    
    /* Table Styles */
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #6c757d;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
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
    }
    
    /* Form Styles */
    .form-select-lg {
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        padding: 12px 16px;
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
    
    .btn-outline-primary {
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary:hover {
        transform: translateY(-2px);
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border-color: transparent;
    }
    
    /* Address Card */
    .address-card {
        border: 1px solid #e9ecef;
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
    
    .alert-danger {
        background: linear-gradient(135deg, #f8d7da 0%, #e9b7bc 100%);
        border: none;
        color: #842029;
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
        top: 44px;
        bottom: -20px;
        width: 2px;
        background: #dee2e6;
    }
    
    /* Print Styles */
    @media print {
        body {
            background: white;
            padding: 0 !important;
        }
        
        .btn, form, .alert, .btn-primary, .btn-light, .btn-outline-primary, 
        .btn-close, [onclick="window.print()"], a[href*="index"] {
            display: none !important;
        }
        
        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
            break-inside: avoid;
            margin-bottom: 1rem !important;
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
        
        .bg-light {
            background: transparent !important;
        }
        
        .timeline-icon {
            border: 1px solid #dee2e6;
            background: transparent !important;
        }
        
        .timeline-icon i {
            color: #000 !important;
        }
        
        .timeline-item:not(:last-child):before {
            background: #dee2e6;
        }
    }
    
    /* Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem !important;
        }
        
        .fs-3 {
            font-size: 1.5rem !important;
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
        
        .info-item {
            padding: 0.75rem !important;
        }
    }
    
    @media (max-width: 576px) {
        h3 {
            font-size: 1.2rem !important;
        }
        
        .badge {
            font-size: 0.7rem !important;
            padding: 0.25rem 0.5rem !important;
        }
        
        .table th, .table td {
            padding: 0.5rem !important;
        }
        
        .fs-4 {
            font-size: 1rem !important;
        }
        
        .timeline-item:not(:last-child):before {
            left: 17px;
            top: 38px;
        }
    }
    
    /* Smooth Transitions */
    * {
        transition: all 0.2s ease-in-out;
    }
    
    /* Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Animation for loading */
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
    
    .card {
        animation: fadeIn 0.3s ease-out;
    }
</style>

{{-- JavaScript for better UX --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-dismiss alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });
        
        // Confirm status change for cancellation
        const statusForm = document.querySelector('form[action*="updateStatus"]');
        if (statusForm) {
            statusForm.addEventListener('submit', function(e) {
                const select = this.querySelector('select[name="status"]');
                if (select && select.value === 'cancelled') {
                    if (!confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
                        e.preventDefault();
                    }
                }
            });
        }
    });
</script>
@endsection