@extends('layouts.master')
@section('content')
<div class="container">
    <h4>Add Manufacturer</h4>

    <form action="{{ route('manufacturers.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text" 
                   name="name" 
                   class="form-control" 
                   value="{{ old('name') }}"
                   required>

            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('manufacturers.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection