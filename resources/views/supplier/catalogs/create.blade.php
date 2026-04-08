@extends('supplier.layouts.app')

@section('content')

    <h3>Add Catalog</h3>

    <form method="POST" action="{{ route('supplier.catalogs.store') }}">
        @csrf

        <select name="item_id" class="form-control mb-2">
            <option value="">-- Select Item --</option>
            @foreach($items as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>

        <input type="text" name="batch_no" placeholder="Enter Batch Number (e.g. BATCH123)" class="form-control mb-2">

        <input type="date" name="expiry_date" class="form-control mb-2" placeholder="Select Expiry Date">
        <input type="number" name="qty" placeholder="Initial Stock Qty" class="form-control mb-2">
        <input type="number" step="0.01" name="purchase_price" placeholder="Enter Purchase Price (e.g. 50.00)"
            class="form-control mb-2">

        <input type="number" step="0.01" name="retailer_price" placeholder="Enter Retailer Price (e.g. 70.00)"
            class="form-control mb-2">

        <input type="number" step="0.01" name="retailer_mrp" placeholder="Enter MRP (e.g. 100.00)"
            class="form-control mb-2">

        <input type="number" step="0.01" name="gst_percent" placeholder="Enter GST % (e.g. 12 or 18)"
            class="form-control mb-2">

        <button class="btn btn-success">Save</button>

    </form>

@endsection