@extends('supplier.layouts.app')

@section('content')

<h3>My Items</h3>

<a href="{{ route('supplier.items.create') }}" class="btn btn-primary mb-3">Add Item</a>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Image</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>₹{{ $item->selling_price }}</td>
            <td>
                @if($item->main_image)
                    <img src="{{ asset('storage/'.$item->main_image) }}" width="50">
                @endif
            </td>
            <td>
                <a href="{{ route('supplier.items.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>

                <form action="{{ route('supplier.items.destroy', $item->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection