@extends('supplier.layouts.app')

@section('content')

<h3>Edit Item</h3>

<form method="POST" action="{{ route('supplier.items.update', $item->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <input type="text" name="name" value="{{ $item->name }}" class="form-control mb-2">

    <input type="text" name="brand" value="{{ $item->brand }}" class="form-control mb-2">

    <input type="number" step="0.01" name="selling_price" value="{{ $item->selling_price }}" class="form-control mb-2">

    <input type="file" name="main_image" class="form-control mb-2">

    <textarea name="description" class="form-control mb-2">{{ $item->description }}</textarea>

    <button class="btn btn-primary">Update</button>
</form>

@endsection