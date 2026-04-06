@extends('layouts.master')

@section('content')

<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
        <h5>Invoice #{{ $sale->bill_number }}</h5>
    </div>

    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Date:</strong> {{ $sale->bill_date }}
            </div>
            <div class="col-md-6 text-end">
                <strong>Customer:</strong>
                {{ $sale->customer->name ?? 'Walk-in' }}
            </div>
        </div>

        <table class="table table-bordered">
            <thead class="table-secondary">
                <tr>
                    <th>Medicine</th>
                    <th width="100">Qty</th>
                    <th width="120">Rate</th>
                    <th width="120">Amount</th>
                </tr>
            </thead>

            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->item->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->sale_rate }}</td>
                    <td>{{ $item->amount }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-end mt-3">
            <h4>Total: ₹ {{ $sale->net_amount }}</h4>
        </div>

        <div class="text-end mt-3">
            <button onclick="window.print()"
                    class="btn btn-dark btn-sm">
                Print
            </button>
        </div>

    </div>
</div>

@endsection