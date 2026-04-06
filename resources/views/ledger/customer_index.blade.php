@extends('layouts.master')

@section('content')

<div class="card shadow-sm">

    <!-- 🔥 Header -->
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Customer Ledger</h5>

        <a href="{{ route('customer.payment.create') }}" class="btn btn-success btn-sm">
            + Add Payment
        </a>
    </div>

    <div class="card-body">

        <!-- 🔍 FILTER -->
        <form method="GET" class="row g-2 mb-3">

            <div class="col-md-3">
                <label>Customer</label>
                <select name="customer_id" class="form-control">
                    <option value="">All Customers</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
            </div>

            <div class="col-md-3">
                <label>To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filter</button>
            </div>

        </form>

        <!-- 📊 TABLE -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">

                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                        <th class="text-end">Balance</th>
                        <th>Description</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($ledgers as $l)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($l->transaction_date)->format('d-m-Y') }}</td>

                        <td>{{ $l->customer->name ?? '-' }}</td>

                        <td>
                            @if($l->type == 'sale')
                                <span class="badge bg-danger">SALE</span>
                            @elseif($l->type == 'payment')
                                <span class="badge bg-success">PAYMENT</span>
                            @else
                                <span class="badge bg-warning">RETURN</span>
                            @endif
                        </td>

                        <td class="text-end text-danger">
                            {{ number_format($l->debit, 2) }}
                        </td>

                        <td class="text-end text-success">
                            {{ number_format($l->credit, 2) }}
                        </td>

                        <td class="text-end fw-bold">
                            {{ number_format($l->balance, 2) }}
                        </td>

                        <td>{{ $l->description }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No records found</td>
                    </tr>
                    @endforelse
                </tbody>

                <!-- 🔥 TOTAL FOOTER -->
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th class="text-end text-danger">{{ number_format($totalDebit, 2) }}</th>
                        <th class="text-end text-success">{{ number_format($totalCredit, 2) }}</th>
                        <th class="text-end">{{ number_format($closingBalance, 2) }}</th>
                        <th></th>
                    </tr>
                </tfoot>

            </table>
        </div>

    </div>
</div>

@endsection