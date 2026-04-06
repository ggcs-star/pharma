@extends('layouts.master')

@section('title','Sub Categories')

@section('content')

<div class="card">

<div class="card-header d-flex justify-content-between">
<h5>Sub Categories</h5>

<a href="{{ route('sub-categories.create') }}" class="btn btn-primary">
Add Sub Category
</a>

</div>

<div class="card-body">

<table class="table table-bordered">

<thead>
<tr>
<th>#</th>
<th>Category</th>
<th>Sub Category</th>
<th width="150">Action</th>
</tr>
</thead>

<tbody>

@foreach($subCategories as $sub)

<tr>

<td>{{ $loop->iteration }}</td>

<td>{{ $sub->category->name }}</td>

<td>{{ $sub->name }}</td>

<td>

<a href="{{ route('sub-categories.edit',$sub->id) }}"
class="btn btn-sm btn-warning">Edit</a>

<form action="{{ route('sub-categories.destroy',$sub->id) }}"
method="POST"
style="display:inline-block">

@csrf
@method('DELETE')

<button class="btn btn-sm btn-danger">
Delete
</button>

</form>

</td>

</tr>

@endforeach

</tbody>

</table>

{{ $subCategories->links() }}

</div>

</div>

@endsection