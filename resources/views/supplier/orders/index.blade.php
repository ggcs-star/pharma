@extends('supplier.layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fas fa-shopping-cart text-primary me-2"></i>My Orders
            </h3>
            <p class="text-muted small mb-0">Manage and track all customer orders</p>
        </div>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-1"></i>Filter Status
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('supplier.orders.index') }}">All Orders</a></li>
                    <li><a class="dropdown-item" href="?status=pending">Pending</a></li>
                    <li><a class="dropdown-item" href="?status=confirmed">Confirmed</a></li>
                    <li><a class="dropdown-item" href="?status=processing">Processing</a></li>
                    <li><a class="dropdown-item" href="?status=dispatched">Dispatched</a></li>
                    <li><a class="dropdown-item" href="?status=delivered">Delivered</a></li>
                    <li><a class="dropdown-item" href="?status=rejected">Rejected</a></li>
                </ul>
            </div>
            <button class="btn btn-primary btn-sm" onclick="window.location.reload()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Orders Table Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Order No</th>
                            <th class="py-3">Retailer</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Total Amount</th>
                            <th class="py-3">Order Date</th>
                            <th class="pe-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-semibold text-dark">#{{ $order->order_number }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                        {{ strtoupper(substr($order->retailer->name ?? 'R', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $order->retailer->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $order->retailer->email ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-2">
                                    <!-- Status Badge -->
                                    <span class="badge 
                                        @if($order->status == 'pending') bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25
                                        @elseif($order->status == 'confirmed') bg-success bg-opacity-10 text-success border border-success border-opacity-25
                                        @elseif($order->status == 'processing') bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25
                                        @elseif($order->status == 'dispatched') bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25
                                        @elseif($order->status == 'delivered') bg-dark bg-opacity-10 text-dark border border-dark border-opacity-25
                                        @elseif($order->status == 'rejected') bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25
                                        @endif
                                        px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.7rem; width: fit-content;">
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

                                    <!-- Status Change Buttons -->
                                    <form method="POST" action="{{ route('supplier.orders.status', $order->id) }}" class="d-flex flex-wrap gap-1">
                                        @csrf
                                        @if($order->status == 'pending')
                                            <button name="status" value="confirmed" class="btn btn-sm btn-success rounded-pill px-3">
                                                <i class="fas fa-check me-1"></i>Confirm
                                            </button>
                                            <button name="status" value="rejected" class="btn btn-sm btn-danger rounded-pill px-3">
                                                <i class="fas fa-times me-1"></i>Reject
                                            </button>
                                        @endif

                                        @if($order->status == 'confirmed')
                                            <button name="status" value="processing" class="btn btn-sm btn-warning rounded-pill px-3">
                                                <i class="fas fa-cogs me-1"></i>Process
                                            </button>
                                        @endif

                                        @if($order->status == 'processing')
                                            <button name="status" value="dispatched" class="btn btn-sm btn-primary rounded-pill px-3">
                                                <i class="fas fa-truck me-1"></i>Dispatch
                                            </button>
                                        @endif

                                      
                                    </form>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-primary">₹{{ number_format($order->net_amount, 2) }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $order->created_at ? $order->created_at->format('d M Y') : 'N/A' }}</small>
                            </td>
                            <td class="pe-4 text-center">
                                <a href="{{ route('supplier.orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No orders found</p>
                                <small class="text-muted">Orders will appear here once customers place them</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if(method_exists($orders, 'links'))
        <div class="d-flex justify-content-end mt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>

<style>
    .avatar-circle {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
    }
    
    .table > :not(caption) > * > * {
        border-bottom-color: #f0f2f5;
    }
    
    .btn-sm {
        font-size: 0.7rem;
    }
    
    .btn-outline-primary:hover {
        background: #0ea5e9;
        border-color: #0ea5e9;
    }
    
    /* Badge styles */
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }
    
    .badge.bg-secondary.bg-opacity-10 {
        background-color: rgba(108, 117, 125, 0.1) !important;
    }
    
    .badge.bg-success.bg-opacity-10 {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }
    
    .badge.bg-warning.bg-opacity-10 {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
    
    .badge.bg-primary.bg-opacity-10 {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    
    .badge.bg-dark.bg-opacity-10 {
        background-color: rgba(33, 37, 41, 0.1) !important;
    }
    
    .badge.bg-danger.bg-opacity-10 {
        background-color: rgba(220, 53, 69, 0.1) !important;
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