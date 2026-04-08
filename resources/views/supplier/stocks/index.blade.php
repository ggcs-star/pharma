@extends('supplier.layouts.app')

@section('content')

<h3>Stock History</h3>

<a href="{{ route('supplier.stocks.create') }}" class="btn btn-primary mb-3">Add Stock</a>

<table class="table">
    <thead>
        <tr>
            <th>Item</th>
            <th>Batch</th>
            <th>Qty</th>
            <th>Type</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($stocks as $s)
        <tr>
            <td>{{ $s->catalog->item->name ?? '' }}</td>
            <td>{{ $s->catalog->batch_no }}</td>
            <td>{{ $s->qty }}</td>
            <td>{{ ucfirst($s->type) }}</td>
            <td>{{ $s->created_at }}</td>
            <td>
                <form method="POST" action="{{ route('supplier.stocks.destroy', $s->id) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection