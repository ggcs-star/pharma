@extends('layouts.master')

@section('title', 'Purchase Return Details')

@section('content')
<div class="container-fluid px-4 py-3">

    <h3 class="mb-4">Purchase Return Details</h3>

    <div class="card p-3 mb-3">
        <div><strong>Return No:</strong> {{ $return->return_number }}</div>
        <div><strong>Supplier:</strong> {{ $return->supplier->name ?? '-' }}</div>
        <div><strong>Invoice:</strong> {{ $return->purchase->invoice_number ?? '-' }}</div>
        <div><strong>Date:</strong> {{ $return->return_date }}</div>
        <div><strong>Total:</strong> ₹ {{ number_format($return->total_amount, 2) }}</div>
    </div>

</div>
@endsection