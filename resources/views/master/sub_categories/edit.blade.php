@extends('layouts.master')

@section('title','Edit Sub Category')

@section('content')

<div class="card">

<div class="card-header">
<h5>Edit Sub Category</h5>
</div>

<div class="card-body">

<form action="{{ route('sub-categories.update',$subCategory->id) }}"
method="POST">

@csrf
@method('PUT')

<div class="mb-3">

<label>Category *</label>

<select name="category_id" class="form-control">

@foreach($categories as $id => $name)

<option value="{{ $id }}"
{{ $subCategory->category_id == $id ? 'selected' : '' }}>

{{ $name }}

</option>

@endforeach

</select>

</div>


<div class="mb-3">

<label>Sub Category Name *</label>

<input type="text"
name="name"
value="{{ $subCategory->name }}"
class="form-control">

</div>

<button class="btn btn-success">
Update
</button>

</form>

</div>

</div>

@endsection