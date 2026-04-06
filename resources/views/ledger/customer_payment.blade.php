@extends('layouts.master')

@section('content')

<div class="card shadow-sm">

    <div class="card-header">
        <h5>Add Customer Payment</h5>
    </div>

    <div class="card-body">

        <form method="POST" action="{{ route('customer.payment.store') }}">
            @csrf

            <div class="mb-3">
                <label>Customer</label>
                <select name="customer_id" class="form-control">
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Amount</label>
                <input type="number" step="0.01" name="amount" class="form-control">
            </div>

            <button class="btn btn-primary">Save Payment</button>
            <a href="{{ route('customer.ledger') }}" class="btn btn-secondary">Back</a>

        </form>

    </div>
</div>

@endsection