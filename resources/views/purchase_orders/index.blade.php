@extends('layouts.master')

@section('content')
    

<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">

        <h5 class="mb-0">Purchase Order</h5>

        <div class="d-flex gap-2">

            <!-- SEARCH -->
            <form method="GET" class="d-flex gap-2">

                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Search..." style="width:200px;">

                <button class="btn btn-primary">Search</button>

                <a href="{{ route('purchase-orders.create') }}" class="btn btn-success">
                    + New PO
                </a>

            </form>

        </div>
    </div>

    <!-- FILTER -->
    <form method="GET">
        <div class="d-flex align-items-center gap-2 mb-2">

            <input type="date" name="from_date"
                   value="{{ request('from_date') }}"
                   class="form-control" style="width:160px;">

            <span>to</span>

            <input type="date" name="to_date"
                   value="{{ request('to_date') }}"
                   class="form-control" style="width:160px;">

            <button class="btn btn-outline-primary">Filter</button>

        </div>
    </form>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>Invoice No</th>
                        <th>Created On</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>

                        <td>
                            <b>{{ $order->order_number }}</b>
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                        </td>

                        <td>
                            {{ $order->supplier->supplier_code ?? '' }} 
                            - {{ $order->supplier->name ?? '-' }}
                        </td>

                        <td>
                            <span class="badge 
                                bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'approved' ? 'primary' : 'warning') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>

                        <td class="d-flex gap-1">

                            <a href="#" class="btn btn-sm btn-info">View</a>

                            @if($order->status != 'completed')
                            <form action="{{ route('purchase-orders.convert', $order->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-success">
                                    Convert
                                </button>
                            </form>
                            @endif

                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No records to display for selected time period.
                        </td>
                    </tr>
                @endforelse

                </tbody>
            </table>

        </div>
    </div>

    <!-- FOOTER -->
    <div class="d-flex justify-content-between align-items-center mt-3">

        <div>
            Showing {{ $orders->count() }} of {{ $orders->total() }}
        </div>

        <div>
            {{ $orders->appends(request()->all())->links() }}
        </div>

    </div>

</div>

@endsection