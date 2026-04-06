@extends('layouts.master')

@section('content')

<div class="card">

    <div class="card-header">
        <h5 class="mb-0">Edit Category</h5>
    </div>

    <div class="card-body">

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Message --}}
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">

                <label class="form-label">Category Name</label>

                <input type="text"
                       name="name"
                       value="{{ old('name', $category->name) }}"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="Enter category name"
                       required>

                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <div class="d-flex gap-2">

                <button type="submit" class="btn btn-primary">
                    Update
                </button>

                <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                    Back
                </a>

            </div>

        </form>

    </div>

</div>

@endsection