@extends('layouts.master')

@section('title','Add Customer')

@section('content')

<div class="card shadow-sm">

<div class="card-header bg-primary text-white">
<h5 class="mb-0">Add Customer</h5>
</div>

<div class="card-body">

{{-- Error Message --}}
@if(session('error'))
<div class="alert alert-danger">
{{ session('error') }}
</div>
@endif

<form action="{{ route('customers.store') }}" method="POST">
@csrf

<div class="row">

{{-- Customer Name --}}
<div class="col-md-6 mb-3">

<label class="form-label">Customer Name *</label>

<input type="text"
name="name"
value="{{ old('name') }}"
class="form-control @error('name') is-invalid @enderror"
placeholder="Enter customer name"
required>

@error('name')
<div class="invalid-feedback">{{ $message }}</div>
@enderror

</div>


{{-- Contact --}}
<div class="col-md-6 mb-3">

<label class="form-label">Contact</label>

<input type="text"
name="contact"
value="{{ old('contact') }}"
class="form-control"
maxlength="10"
placeholder="Mobile number">

</div>


{{-- Flat Number --}}
<div class="col-md-4 mb-3">

<label class="form-label">Flat Number</label>

<input type="text"
name="flat_number"
value="{{ old('flat_number') }}"
class="form-control"
placeholder="Flat / House No">

</div>


{{-- Discount --}}
<div class="col-md-4 mb-3">

<label class="form-label">Discount %</label>

<input type="number"
step="0.01"
name="discount"
value="{{ old('discount',0) }}"
class="form-control">

</div>


{{-- Customer Type --}}
<div class="col-md-4 mb-3">

<label class="form-label">Customer Type</label>

<select name="customer_type" class="form-control">

<option value="">Select Type</option>

<option value="regular"
{{ old('customer_type')=='regular'?'selected':'' }}>
Regular
</option>

<option value="vip"
{{ old('customer_type')=='vip'?'selected':'' }}>
VIP
</option>

</select>

</div>


{{-- Doctor --}}
<div class="col-md-6 mb-3">

<label class="form-label">Doctor</label>

<select name="doctor_id" class="form-control">

<option value="">Select Doctor</option>

@foreach($doctors as $doctor)

<option value="{{ $doctor->id }}"
{{ old('doctor_id')==$doctor->id?'selected':'' }}>

{{ $doctor->name }}

</option>

@endforeach

</select>

</div>


{{-- City --}}
<div class="col-md-3 mb-3">

<label class="form-label">City</label>

<input type="text"
name="city"
value="{{ old('city') }}"
class="form-control"
placeholder="City">

</div>


{{-- Pincode --}}
<div class="col-md-3 mb-3">

<label class="form-label">Pincode</label>

<input type="text"
name="pincode"
value="{{ old('pincode') }}"
class="form-control"
placeholder="Pincode">

</div>


{{-- Preferred Language --}}
<div class="col-md-6 mb-3">

<label class="form-label">Preferred Language</label>

<input type="text"
name="preferred_language"
value="{{ old('preferred_language') }}"
class="form-control"
placeholder="Language">

</div>


{{-- Address --}}
<div class="col-md-12 mb-3">

<label class="form-label">Address</label>

<textarea name="address"
rows="3"
class="form-control"
placeholder="Full address">{{ old('address') }}</textarea>

</div>


</div>


<div class="d-flex gap-2">

<button type="submit" class="btn btn-success">
<i class="fa fa-save"></i> Save Customer
</button>

<a href="{{ route('customers.index') }}"
class="btn btn-secondary">

<i class="fa fa-arrow-left"></i> Back

</a>

</div>


</form>

</div>

</div>

@endsection