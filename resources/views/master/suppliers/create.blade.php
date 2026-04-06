@extends('layouts.master')

@section('content')

<div class="card">
    <div class="card-header">
        <h5>Add Supplier</h5>
    </div>

    <div class="card-body">
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label>Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Supplier Code</label>
                    <input type="text" name="supplier_code" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>GSTIN</label>
                    <input type="text" name="gst_in" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Drug License</label>
                    <input type="text" name="drug_license" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Credit Period (Days)</label>
                    <input type="number" name="credit_period" class="form-control" value="0">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Account Number</label>
                    <input type="text" name="account_no" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>IFSC Code</label>
                    <input type="text" name="ifsc_code" class="form-control">
                </div>

                <div class="col-md-12 mb-3">
                    <label>Address</label>
                    <textarea name="address" class="form-control"></textarea>
                </div>

            </div>

            <button class="btn btn-success">Save</button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back</a>

        </form>
    </div>
</div>

@endsection