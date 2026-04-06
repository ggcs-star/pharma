@extends('layouts.master')

@section('content')

<div class="container-fluid px-4">

    <div class="card shadow-lg border-0 rounded-3">

        <!-- HEADER -->
        <div class="card-header d-flex justify-content-between align-items-center text-white"
             style="background: linear-gradient(135deg,#1e3c72,#2a5298);">

            <div>
                <h5 class="mb-0 fw-bold">📦 Catalog Management</h5>
                <small class="opacity-75">Manage your products</small>
            </div>

          <div class="d-flex gap-2">

    <!-- IMPORT BUTTON -->
<a href="{{ route('master.items.import.page') }}" class="btn btn-warning btn-sm fw-bold">
    ⬆ Import
</a>

    <!-- ADD ITEM -->
    <a href="{{ route('master.items.create') }}" class="btn btn-light btn-sm fw-bold">
        + Add Item
    </a>

</div>
        </div>

        <div class="card-body">

            <!-- SUCCESS -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="bg-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th class="text-start">Item</th>
                            <th>Manufacturer</th>
                            <th>Category</th>
                            <th>GST</th>
                            <th>Rack</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($items as $index => $item)

                        <tr>

                            <!-- INDEX -->
                            <td class="text-center">
                                {{ $items->firstItem() + $index }}
                            </td>

                            <!-- ITEM -->
                            <td class="text-start">
                                <div class="d-flex align-items-center">

                                    <!-- IMAGE -->
                                    <div class="me-3">
                                        @if($item->main_image)
                                            <img src="{{ $item->main_image_url }}"
                                                 class="rounded shadow-sm"
                                                 style="width:55px;height:55px;object-fit:cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                 style="width:55px;height:55px;">
                                                <i class="bi bi-capsule"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- DETAILS -->
                                    <div>
                                        <div class="fw-bold">{{ $item->name }}</div>

                                        @if($item->brand)
                                            <small class="text-muted">{{ $item->brand }}</small>
                                        @endif

                                        @if($item->molecule)
                                            <small class="text-primary d-block">
                                                {{ $item->molecule }}
                                            </small>
                                        @endif
                                    </div>

                                </div>
                            </td>

                            <!-- MANUFACTURER -->
                            <td class="text-center">
                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill shadow-sm">
                                    {{ $item->manufacturer->name ?? '-' }}
                                </span>
                            </td>

                            <!-- CATEGORY -->
                            <td class="text-center">
                                <span class="badge bg-light text-primary px-3 py-2 rounded-pill shadow-sm">
                                    {{ $item->category->name ?? '-' }}
                                </span>
                            </td>

                            <!-- GST -->
                            <td class="text-center text-success fw-bold">
                                {{ number_format($item->gst_percent, 2) }}%
                            </td>

                            <!-- RACK -->
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-dark px-3 py-2 rounded-pill">
                                    {{ $item->rack ?? '—' }}
                                </span>
                            </td>

                            <!-- STATUS -->
                            <td class="text-center">
                                @if($item->inactive)
                                    <span class="badge bg-danger">Inactive</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>

                            <!-- ACTION -->
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">

                                    <a href="{{ route('master.items.edit', $item->id) }}"
                                       class="btn btn-outline-warning btn-sm rounded-pill">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('master.items.destroy', $item->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete item?')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-outline-danger btn-sm rounded-pill">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                No items found
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>
            </div>

            <!-- PAGINATION -->
            <div class="d-flex justify-content-between align-items-center mt-3">

                <small class="text-muted">
                    Showing {{ $items->firstItem() ?? 0 }}
                    to {{ $items->lastItem() ?? 0 }}
                    of {{ $items->total() }} items
                </small>

                {{ $items->links('pagination::bootstrap-5') }}

            </div>

        </div>

    </div>

</div>

@endsection


@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
.table td {
    vertical-align: middle;
}

.table img {
    transition: transform 0.2s ease;
}

.table img:hover {
    transform: scale(1.1);
}

.badge {
    font-size: 12px;
}
</style>
@endpush