@extends('layouts.master')

@section('title','Edit Doctor')

@section('content')

<div class="card">

<div class="card-header">
<h5>Edit Doctor</h5>
</div>

<div class="card-body">

<form action="{{ route('doctors.update',$doctor->id) }}" method="POST">

@csrf
@method('PUT')

<div class="row">

<div class="col-md-6 mb-3">
<label>Name *</label>

<input type="text"
name="name"
value="{{ $doctor->name }}"
class="form-control" required>

</div>


<div class="col-md-6 mb-3">
<label>Contact</label>

<input type="text"
name="contact"
value="{{ $doctor->contact }}"
class="form-control">

</div>


<div class="col-md-6 mb-3">
<label>Email</label>

<input type="email"
name="email"
value="{{ $doctor->email }}"
class="form-control">

</div>


<div class="col-md-6 mb-3">
<label>Registration No</label>

<input type="text"
name="registration_number"
value="{{ $doctor->registration_number }}"
class="form-control">

</div>


<div class="col-md-6 mb-3">
<label>Professional Credential</label>

<input type="text"
name="professional_credential"
value="{{ $doctor->professional_credential }}"
class="form-control">

</div>


<div class="col-md-6 mb-3">
<label>Medical Speciality</label>

<input type="text"
name="medical_speciality"
value="{{ $doctor->medical_speciality }}"
class="form-control">

</div>


<div class="col-md-6 mb-3">
<label>Clinic Name</label>

<input type="text"
name="clinic_name"
value="{{ $doctor->clinic_name }}"
class="form-control">

</div>


<div class="col-md-3 mb-3">
<label>Clinic City</label>

<input type="text"
name="clinic_city"
value="{{ $doctor->clinic_city }}"
class="form-control">

</div>


<div class="col-md-3 mb-3">
<label>Clinic Pincode</label>

<input type="text"
name="clinic_pincode"
value="{{ $doctor->clinic_pincode }}"
class="form-control">

</div>


<div class="col-md-12 mb-3">
<label>Clinic Address</label>

<textarea name="clinic_address"
class="form-control">{{ $doctor->clinic_address }}</textarea>

</div>

</div>


<button class="btn btn-primary">Update</button>

<a href="{{ route('doctors.index') }}"
class="btn btn-secondary">Back</a>

</form>

</div>

</div>

@endsection