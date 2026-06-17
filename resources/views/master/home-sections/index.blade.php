@extends('layouts.master')

@section('title', 'Home Sections')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold mb-0">
            Homepage Sections
        </h2>

        <a
            href="{{ route('home-sections.create') }}"
            class="btn btn-primary"
        >
            + Add Section
        </a>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>#</th>

                            <th>Title</th>

                            <th>Category</th>

                            <th>Sort Order</th>

                            <th>Status</th>

                            <th width="180">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($sections as $section)

                            <tr>

                                <td>
                                    {{ $section->id }}
                                </td>

                                <td>

                                    <div class="fw-semibold">

                                        {{ $section->title }}

                                    </div>

                                </td>

                                <td>

                                    {{ $section->category?->name }}

                                </td>

                                <td>

                                    {{ $section->sort_order }}

                                </td>

                                <td>

                                    @if($section->is_active)

                                        <span class="badge bg-success">
                                            Active
                                        </span>

                                    @else

                                        <span class="badge bg-danger">
                                            Inactive
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <div class="d-flex gap-2">

                                        <a
                                            href="{{ route('home-sections.edit', $section->id) }}"
                                            class="btn btn-sm btn-warning"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route('home-sections.destroy', $section->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete Section?')"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="text-center py-5">

                                    No Sections Found

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection