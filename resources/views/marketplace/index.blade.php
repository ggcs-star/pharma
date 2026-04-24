@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Pharma Marketplace</h2>
            <p class="text-muted mb-0">
                Compare supplier prices, stock and create Purchase Orders
            </p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Search Medicine</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control rounded-3"
                            placeholder="Search medicine name..."
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Supplier</label>
                        <select name="supplier_id" class="form-select rounded-3">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Min Stock</label>
                        <input
                            type="number"
                            name="stock"
                            class="form-control rounded-3"
                            value="{{ request('stock') }}"
                            placeholder="0"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Sort By</label>
                        <select name="sort" class="form-select rounded-3">
                            <option value="">Latest</option>
                            <option value="low_price" {{ request('sort') == 'low_price' ? 'selected' : '' }}>Lowest PTR</option>
                            <option value="high_stock" {{ request('sort') == 'high_stock' ? 'selected' : '' }}>Highest Stock</option>
                        </select>
                    </div>

                    <div class="col-md-12 d-flex gap-2 mt-2">
                        <button class="btn btn-primary px-4 rounded-3">
                            Apply Filters
                        </button>

                        <a href="{{ route('marketplace.index') }}"
                           class="btn btn-light border px-4 rounded-3">
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Products Grid --}}
    <div class="row">

        @forelse($products as $product)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow rounded-4 h-100">

                    <div class="p-3 text-center">
                        @php
                            $image = $product->item->main_image
                                ? asset($product->item->main_image)
                                : asset('images/no-image.png');
                        @endphp

                        <img
                            src="{{ $image }}"
                            class="img-fluid rounded-3"
                            style="height:220px; object-fit:contain;"
                            alt="medicine"
                        >
                    </div>

                    <div class="card-body pt-0">
                        <h5 class="fw-bold mb-2">
                            {{ $product->item->name ?? '-' }}
                        </h5>

                        <div class="small text-muted mb-2">
                            Supplier:
                            <strong>{{ $product->supplier->name ?? '-' }}</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Retailer PTR</span>
                            <strong class="text-primary">
                                ₹ {{ number_format($product->retailer_price, 2) }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>MRP</span>
                            <strong>
                                ₹ {{ number_format($product->base_price, 2) }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Stock</span>
                            <span class="badge bg-success">
                                {{ $product->current_stock }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Expiry</span>
                            <strong>
                                {{ \Carbon\Carbon::parse($product->expiry_date)->format('d M Y') }}
                            </strong>
                        </div>

                     <a href="{{ route('purchase-orders.create', [
        'supplier_id' => $product->supplier_id,
        'item_id' => $product->item_id,
        'catalog_id' => $product->id
    ]) }}"
   class="btn btn-primary w-100 rounded-3">
    Create PO
</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning rounded-4">
                    No products found.
                </div>
            </div>
        @endforelse

    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>

</div>
@endsection
