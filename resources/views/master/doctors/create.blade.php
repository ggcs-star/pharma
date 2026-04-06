@extends('layouts.master')

@section('title','Add Doctor')

@section('content')

<div class="card">

<div class="card-header bg-primary text-white">
<h5 class="mb-0">Add Doctor</h5>
</div>

<div class="card-body">

<form action="{{ route('doctors.store') }}" method="POST">
@csrf

<div class="row">

<div class="col-md-6 mb-3">
<label>Doctor Name *</label>
<input type="text" name="name"
value="{{ old('name') }}"
class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Contact</label>
<input type="text" name="contact"
value="{{ old('contact') }}"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Email</label>
<input type="email" name="email"
value="{{ old('email') }}"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Registration Number</label>
<input type="text" name="registration_number"
value="{{ old('registration_number') }}"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Professional Credential</label>
<input type="text" name="professional_credential"
value="{{ old('professional_credential') }}"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Medical Speciality</label>
<input type="text" name="medical_speciality"
value="{{ old('medical_speciality') }}"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Clinic Name</label>
<input type="text" name="clinic_name"
value="{{ old('clinic_name') }}"
class="form-control">
</div>

<div class="col-md-3 mb-3">
<label>Clinic City</label>
<input type="text" name="clinic_city"
value="{{ old('clinic_city') }}"
class="form-control">
</div>

<div class="col-md-3 mb-3">
<label>Clinic Pincode</label>
<input type="text" name="clinic_pincode"
value="{{ old('clinic_pincode') }}"
class="form-control">
</div>

<div class="col-md-12 mb-3">
<label>Clinic Address</label>
<textarea name="clinic_address"
class="form-control">{{ old('clinic_address') }}</textarea>
</div>

</div>

<button class="btn btn-success">Save Doctor</button>

<a href="{{ route('doctors.index') }}" class="btn btn-secondary">
Back
</a>

</form>

</div>
</div>

@endsection