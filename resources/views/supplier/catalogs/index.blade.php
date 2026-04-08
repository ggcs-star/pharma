@extends('supplier.layouts.app')

@section('content')

<h3>Catalog List</h3>

<a href="{{ route('supplier.catalogs.create') }}" class="btn btn-primary mb-3">Add Catalog</a>

<table class="table">
    <thead>
        <tr>
            <th>Item</th>
            <th>Batch</th>
            <th>Expiry</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($catalogs as $c)
        <tr>
            <td>{{ $c->item->name }}</td>
            <td>{{ $c->batch_no }}</td>
            <td>{{ $c->expiry_date }}</td>
            <td>₹{{ $c->retailer_price }}</td>
            <td>{{ $c->real_stock }}</td>
            <td>
                <a href="{{ route('supplier.catalogs.edit', $c->id) }}" class="btn btn-warning btn-sm">Edit</a>

                <form action="{{ route('supplier.catalogs.destroy', $c->id) }}" method="POST" style="display:inline;">
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