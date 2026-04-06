@extends('layouts.master')

@section('title','Doctors')

@section('content')

<div class="card shadow-sm">

<div class="card-header d-flex justify-content-between align-items-center">

<h5 class="mb-0">Doctors</h5>

<button class="btn btn-primary btn-sm"
data-bs-toggle="modal"
data-bs-target="#doctorModal">

<i class="fa fa-plus"></i> Add Doctor

</button>

</div>


<div class="card-body">

@if(session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif


<div class="table-responsive">

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>
<th width="60">ID</th>
<th>Name</th>
<th>Contact</th>
<th>Medical Speciality</th>
<th>Clinic</th>
<th width="150">Action</th>
</tr>

</thead>

<tbody>

@forelse($doctors as $doctor)

<tr>

<td>{{ $doctor->id }}</td>
<td>{{ $doctor->name }}</td>
<td>{{ $doctor->contact ?? '-' }}</td>
<td>{{ $doctor->medical_speciality ?? '-' }}</td>
<td>{{ $doctor->clinic_name ?? '-' }}</td>

<td>

<a href="{{ route('doctors.edit',$doctor->id) }}"
class="btn btn-warning btn-sm">
Edit
</a>

<form action="{{ route('doctors.destroy',$doctor->id) }}"
method="POST"
style="display:inline;">

@csrf
@method('DELETE')

<button class="btn btn-danger btn-sm"
onclick="return confirm('Delete doctor?')">
Delete
</button>

</form>

</td>

</tr>

@empty

<tr>
<td colspan="6" class="text-center">
No Doctors Found
</td>
</tr>

@endforelse

</tbody>

</table>

</div>

{{ $doctors->links() }}

</div>

</div>



<!-- Add Doctor Modal -->

<div class="modal fade" id="doctorModal">

<div class="modal-dialog modal-lg">

<div class="modal-content">

<form action="{{ route('doctors.store') }}" method="POST">

@csrf

<div class="modal-header">

<h5 class="modal-title">Add Doctor</h5>

<button type="button" class="btn-close"
data-bs-dismiss="modal"></button>

</div>


<div class="modal-body">

<div class="row">

<div class="col-md-6 mb-3">
<label>Name *</label>
<input type="text" name="name"
class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Contact</label>
<input type="text" name="contact"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Email</label>
<input type="email" name="email"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Registration Number</label>
<input type="text" name="registration_number"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Professional Credential</label>
<input type="text" name="professional_credential"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Medical Speciality</label>
<input type="text" name="medical_speciality"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Clinic Name</label>
<input type="text" name="clinic_name"
class="form-control">
</div>

<div class="col-md-3 mb-3">
<label>Clinic City</label>
<input type="text" name="clinic_city"
class="form-control">
</div>

<div class="col-md-3 mb-3">
<label>Clinic Pincode</label>
<input type="text" name="clinic_pincode"
class="form-control">
</div>

<div class="col-md-12 mb-3">
<label>Clinic Address</label>
<textarea name="clinic_address"
class="form-control"></textarea>
</div>

</div>

</div>


<div class="modal-footer">

<button type="submit"
class="btn btn-primary">

Add Doctor

</button>

<button type="button"
class="btn btn-secondary"
data-bs-dismiss="modal">

Cancel

</button>

</div>

</form>

</div>

</div>

</div>

@endsection