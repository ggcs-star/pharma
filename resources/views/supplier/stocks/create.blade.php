@extends('supplier.layouts.app')

@section('content')

<h3>Add Stock</h3>

<form method="POST" action="{{ route('supplier.stocks.store') }}">
    @csrf

    <select name="supplier_item_catalog_id" class="form-control mb-2">
        @foreach($catalogs as $c)
            <option value="{{ $c->id }}">
                {{ $c->item->name }} (Batch: {{ $c->batch_no }})
            </option>
        @endforeach
    </select>

    <input type="number" name="qty" placeholder="Quantity" class="form-control mb-2">

    <select name="type" class="form-control mb-2">
        <option value="purchase">Purchase (+)</option>
        <option value="sale">Sale (-)</option>
        <option value="return">Return</option>
        <option value="adjustment">Adjustment</option>
    </select>

    <textarea name="note" placeholder="Note" class="form-control mb-2"></textarea>

    <button class="btn btn-success">Save</button>

</form>

@endsection