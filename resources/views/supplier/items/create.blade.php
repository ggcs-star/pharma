@extends('supplier.layouts.app')

@section('content')

<h3>Add Item</h3>

<form method="POST" action="{{ route('supplier.items.store') }}" enctype="multipart/form-data">
    @csrf

    <input type="text" name="name" placeholder="Name" class="form-control mb-2" required>

    <input type="text" name="brand" placeholder="Brand" class="form-control mb-2">

    <input type="number" step="0.01" name="selling_price" placeholder="Selling Price" class="form-control mb-2" required>

    <input type="file" name="main_image" class="form-control mb-2">

    <textarea name="description" placeholder="Description" class="form-control mb-2"></textarea>

    <button class="btn btn-success">Save</button>
</form>

@endsection