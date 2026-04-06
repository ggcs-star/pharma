@extends('layouts.master')

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Sales List</h5>
        <a href="{{ route('sales.create') }}"
           class="btn btn-primary btn-sm">New Sale</a>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Bill No</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th width="150">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($sales as $sale)
                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>{{ $sale->bill_number }}</td>
                    <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                    <td>{{ $sale->bill_date }}</td>
                    <td>₹ {{ $sale->net_amount }}</td>
                    <td>
                        <a href="{{ route('sales.show',$sale->id) }}"
                           class="btn btn-info btn-sm">View</a>

                        
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6"
                        class="text-center">No Data</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $sales->links() }}

    </div>
</div>

@endsection