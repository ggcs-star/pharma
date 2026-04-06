@extends('layouts.master')
@section('content')
<div class="container">
    <h4>Edit Manufacturer</h4>

    <form action="{{ route('manufacturers.update', $manufacturer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Name</label>
            <input type="text" 
                   name="name" 
                   value="{{ old('name', $manufacturer->name) }}" 
                   class="form-control" 
                   required>

            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('manufacturers.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection