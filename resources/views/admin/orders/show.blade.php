@extends('layouts.master')

@section('content')
<div class="container">

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h3 class="mb-4">🧾 Order Details (#{{ $order->id }})</h3>

    {{-- ORDER INFO --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">

            <p><strong>User:</strong> {{ $order->user->name ?? 'Guest' }}</p>

            <p><strong>Total:</strong> ₹{{ number_format($order->total ?? 0, 2) }}</p>

            {{-- STATUS --}}
            <p><strong>Status:</strong>
                <span class="badge 
                    @if($order->status == 'pending') badge-warning
                    @elseif($order->status == 'confirmed') badge-info
                    @elseif($order->status == 'processing') badge-primary
                    @elseif($order->status == 'shipped') badge-dark
                    @elseif($order->status == 'delivered') badge-success
                    @else badge-danger
                    @endif
                    text-dark">
                    {{ ucfirst($order->status ?? '-') }}
                </span>
            </p>

            {{-- 🔥 STATUS UPDATE FORM --}}
            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="mb-3">
                @csrf

                <div class="d-flex" style="gap:10px; max-width:400px;">

                    <select name="status" class="form-control" required>
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>

                    <button class="btn btn-primary">
                        Update
                    </button>

                </div>
            </form>

            {{-- PAYMENT --}}
            <p><strong>Payment:</strong>
                @if($order->payment_mode == 'razorpay')
                    <span class="badge badge-success text-dark">
                        💳 Paid Online
                    </span>
                @else
                    <span class="badge badge-secondary text-dark">
                        💵 COD
                    </span>
                @endif
            </p>

        </div>
    </div>

    {{-- ITEMS --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header">🛒 Items</div>
        <div class="card-body">

            <table class="table table-bordered">
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>

                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->item->name ?? 'N/A' }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>₹{{ number_format($item->price, 2) }}</td>
                    <td>₹{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach

            </table>

        </div>
    </div>

    {{-- ADDRESS --}}
    <div class="card shadow-sm">
        <div class="card-header">📍 Shipping Address</div>
        <div class="card-body">

            @if($order->address)
                <p>{{ $order->address->address_line_1 }}</p>
                <p>{{ $order->address->city }}, {{ $order->address->state }}</p>
                <p>{{ $order->address->pincode }}</p>
            @else
                <p>No Address Found</p>
            @endif

        </div>
    </div>

</div>
@endsection