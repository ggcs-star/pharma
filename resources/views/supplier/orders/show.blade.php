@extends('supplier.layouts.app')

@section('content')

<h3>Order Details</h3>

<p><strong>Order No:</strong> {{ $order->order_number }}</p>
<p><strong>Retailer:</strong> {{ $order->retailer->name }}</p>
<p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Item</th>
            <th>Brand</th>
            <th>Batch</th>
            <th>Expiry</th>
            <th>Stock</th>
            <th>Purchase Price</th>
            <th>Retailer Price</th>
            <th>MRP</th>
            <th>Order Rate</th>
            <th>Qty</th>
            <th>GST</th>
            <th>Total</th>
        </tr>
    </thead>

    <tbody>
        @foreach($order->items as $row)
        <tr>
            <td>{{ $row->item->name }}</td>

            <td>{{ $row->item->brand }}</td>

            <td>{{ optional($row->catalog)->batch_no }}</td>
            <td>{{ optional($row->catalog)->expiry_date }}</td>

            <td>{{ optional($row->catalog)->real_stock }}</td>

            <td>₹{{ optional($row->catalog)->purchase_price }}</td>
            <td>₹{{ optional($row->catalog)->retailer_price }}</td>
            <td>₹{{ optional($row->catalog)->retailer_mrp }}</td>

            <td>₹{{ $row->rate }}</td>
            <td>{{ $row->quantity }}</td>
            <td>{{ $row->gst_percent }}%</td>
            <td>₹{{ $row->total_amount }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection