@extends('layouts.master')

@section('content')

<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">Edit Supplier</h5>
    </div>

    <div class="card-body">

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('suppliers.update',$supplier->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">

                {{-- NAME --}}
                <div class="col-md-6 mb-3">
                    <label>Name *</label>
                    <input type="text" name="name"
                           value="{{ $supplier->name }}"
                           class="form-control" required>
                </div>

                {{-- SUPPLIER CODE --}}
                <div class="col-md-6 mb-3">
                    <label>Supplier Code</label>
                    <input type="text" name="supplier_code"
                           value="{{ $supplier->supplier_code }}"
                           class="form-control">
                </div>

                {{-- PHONE --}}
                <div class="col-md-6 mb-3">
                    <label>Phone</label>
                    <input type="text" name="phone"
                           value="{{ $supplier->phone }}"
                           class="form-control">
                </div>

                {{-- EMAIL --}}
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email"
                           value="{{ $supplier->email }}"
                           class="form-control">
                </div>

                {{-- GSTIN (🔥 FIXED) --}}
                <div class="col-md-6 mb-3">
                    <label>GSTIN</label>
                    <input type="text" name="gst_in"
                           value="{{ $supplier->gst_in }}"
                           class="form-control">
                </div>

                {{-- DRUG LICENSE (🔥 FIXED) --}}
                <div class="col-md-6 mb-3">
                    <label>Drug License</label>
                    <input type="text" name="drug_license"
                           value="{{ $supplier->drug_license }}"
                           class="form-control">
                </div>

                {{-- CREDIT PERIOD (🔥 REQUIRED FIX) --}}
                <div class="col-md-6 mb-3">
                    <label>Credit Period (Days)</label>
                    <input type="number" name="credit_period"
                           value="{{ $supplier->credit_period ?? 0 }}"
                           class="form-control" required>
                </div>

                {{-- ACCOUNT NO --}}
                <div class="col-md-6 mb-3">
                    <label>Account No</label>
                    <input type="text" name="account_no"
                           value="{{ $supplier->account_no }}"
                           class="form-control">
                </div>

                {{-- IFSC --}}
                <div class="col-md-6 mb-3">
                    <label>IFSC Code</label>
                    <input type="text" name="ifsc_code"
                           value="{{ $supplier->ifsc_code }}"
                           class="form-control">
                </div>

                {{-- ADDRESS --}}
                <div class="col-md-12 mb-3">
                    <label>Address</label>
                    <textarea name="address" class="form-control">{{ $supplier->address }}</textarea>
                </div>

            </div>

            <button class="btn btn-primary">Update</button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back</a>

        </form>
    </div>
</div>

@endsection