@extends('layouts.master')

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Categories</h5>

        <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
            Add Category
        </a>
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

        <div class="table-responsive">

            <table class="table table-bordered table-striped">

                <thead class="table-light">
                    <tr>
                        <th width="80">#</th>
                        <th>Category Name</th>
                        <th width="160">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($categories as $key => $category)

                        <tr>

                            <td>
                                {{ $categories->firstItem() + $key }}
                            </td>

                            <td>
                                {{ $category->name }}
                            </td>

                            <td class="d-flex gap-1">

                                <a href="{{ route('categories.edit', $category->id) }}"
                                   class="btn btn-warning btn-sm">
                                   Edit
                                </a>

                                <form action="{{ route('categories.destroy', $category->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this category?')">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm">
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="3" class="text-center">
                                No Categories Found
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $categories->links() }}
        </div>

    </div>

</div>

@endsection