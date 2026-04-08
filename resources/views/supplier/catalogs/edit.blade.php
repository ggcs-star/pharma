@extends('supplier.layouts.app')

@section('content')

<h3>Edit Catalog</h3>

<form method="POST" action="{{ route('supplier.catalogs.update', $catalog->id) }}">
    @csrf
    @method('PUT')

    <input type="text" value="{{ $catalog->batch_no }}" name="batch_no" class="form-control mb-2">
    <input type="date" value="{{ $catalog->expiry_date }}" name="expiry_date" class="form-control mb-2">

    <input type="number" step="0.01" value="{{ $catalog->purchase_price }}" name="purchase_price" class="form-control mb-2">
    <input type="number" step="0.01" value="{{ $catalog->retailer_price }}" name="retailer_price" class="form-control mb-2">
    <input type="number" step="0.01" value="{{ $catalog->retailer_mrp }}" name="retailer_mrp" class="form-control mb-2">

    <button class="btn btn-primary">Update</button>

</form>

@endsection