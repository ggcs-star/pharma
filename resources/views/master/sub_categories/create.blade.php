@extends('layouts.master')

@section('title','Add Sub Category')

@section('content')

<div class="card">

<div class="card-header">
<h5>Add Sub Category</h5>
</div>

<div class="card-body">

<form action="{{ route('sub-categories.store') }}" method="POST">

@csrf

<div class="mb-3">

<label>Category *</label>

<select name="category_id" class="form-control" required>

<option value="">Select Category</option>

@foreach($categories as $id => $name)

<option value="{{ $id }}">
{{ $name }}
</option>

@endforeach

</select>

</div>


<div class="mb-3">

<label>Sub Category Name *</label>

<input type="text"
name="name"
class="form-control"
required>

</div>

<button class="btn btn-success">
Save
</button>

<a href="{{ route('sub-categories.index') }}"
class="btn btn-secondary">

Back

</a>

</form>

</div>

</div>

@endsection