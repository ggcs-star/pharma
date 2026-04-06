@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>🧾 Orders Management</h3>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>#ID</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>

                        <td>
                            <strong>{{ $order->user->name ?? 'Guest' }}</strong><br>
                            <small>{{ $order->user->email ?? '' }}</small>
                        </td>

<td>₹{{ number_format($order->total, 2) }}</td>
                     <td>

    {{-- ONLINE PAYMENT --}}
    @if($order->payment_mode == 'razorpay')

        @if($order->payment_status == 'paid')
            <span class="badge badge-success text-dark px-3 py-2">
                💳 Paid Online
            </span>
        @else
            <span class="badge badge-warning text-dark px-3 py-2">
                ⏳ Online Pending
            </span>
        @endif

    {{-- COD --}}
    @else

        <span class="badge badge-secondary text-dark px-3 py-2">
            💵 COD
        </span>

    @endif

</td>
                       <td>
    <span class="badge 
        @if($order->status == 'pending') badge-warning
        @elseif($order->status == 'confirmed') badge-info
        @elseif($order->status == 'processing') badge-primary
        @elseif($order->status == 'shipped') badge-dark
        @elseif($order->status == 'delivered') badge-success
        @elseif($order->status == 'cancelled') badge-danger
        @else badge-secondary
        @endif
        text-dark">

        {{ ucfirst($order->status ?? '-') }}

    </span>
</td>

                        <td>
                            {{ $order->created_at->format('d M Y') }} <br>
                            <small>{{ $order->created_at->format('h:i A') }}</small>
                        </td>

                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" 
                               class="btn btn-sm btn-primary">
                               View
                            </a>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No Orders Found</td>
                    </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection