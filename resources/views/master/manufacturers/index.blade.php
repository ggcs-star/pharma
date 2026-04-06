@extends('layouts.master')

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Manufacturers</h5>
        <a href="{{ route('manufacturers.create') }}" class="btn btn-primary btn-sm">
            Add Manufacturer
        </a>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th width="150">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($manufacturers as $manufacturer)
                    <tr>
                        <td>{{ $manufacturer->id }}</td>
                        <td>{{ $manufacturer->name }}</td>
                        <td>
                            <a href="{{ route('manufacturers.edit', $manufacturer->id) }}" 
                               class="btn btn-warning btn-sm">Edit</a>

                            <form action="{{ route('manufacturers.destroy', $manufacturer->id) }}" 
                                  method="POST" 
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this record?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No Data Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $manufacturers->links() }}

    </div>
</div>

@endsection