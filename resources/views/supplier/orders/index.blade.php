@extends('supplier.layouts.app')

@section('content')

    <h3>My Orders</h3>

    <table class="table">
        <thead>
            <tr>
                <th>Order No</th>
                <th>Retailer</th>
                <th>Status</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->retailer->name ?? '' }}</td>
                    <td>
    {{-- Status Badge --}}
    <span class="badge 
        @if($order->status == 'pending') bg-secondary
        @elseif($order->status == 'confirmed') bg-success
        @elseif($order->status == 'processing') bg-warning
        @elseif($order->status == 'dispatched') bg-primary
        @elseif($order->status == 'delivered') bg-dark
        @elseif($order->status == 'rejected') bg-danger
        @endif
    ">
        {{ ucfirst($order->status) }}
    </span>

    {{-- Status Change Buttons --}}
    <form method="POST" action="{{ route('supplier.orders.status', $order->id) }}" class="mt-2">
        @csrf

        @if($order->status == 'pending')
            <button name="status" value="confirmed" class="btn btn-success btn-sm">✔ Confirm</button>
            <button name="status" value="rejected" class="btn btn-danger btn-sm">✖ Reject</button>
        @endif

        @if($order->status == 'confirmed')
            <button name="status" value="processing" class="btn btn-warning btn-sm">⚙ Process</button>
        @endif

        @if($order->status == 'processing')
            <button name="status" value="dispatched" class="btn btn-primary btn-sm">🚚 Dispatch</button>
        @endif

        @if($order->status == 'dispatched')
            <button name="status" value="delivered" class="btn btn-dark btn-sm">📦 Delivered</button>
        @endif

    </form>
</td>
                    <td>₹{{ $order->net_amount }}</td>
                    <td>
                        <a href="{{ route('supplier.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                            View
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection