@extends('layouts.master')

@section('content')

<style>
@media print {
    body * { visibility: hidden; }
    #print-area, #print-area * { visibility: visible; }

    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    .no-print { display: none !important; }
}
</style>

<div id="print-area">

<div class="card shadow-sm border-0">

    {{-- HEADER --}}
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            Purchase Invoice #{{ $purchase->invoice_number ?? 'N/A' }}
        </h5>

        <span>
            {{ $purchase->purchase_date 
                ? \Carbon\Carbon::parse($purchase->purchase_date)->format('d-m-Y') 
                : '' }}
        </span>
    </div>

    <div class="card-body">

        {{-- SUPPLIER --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <strong>Supplier:</strong><br>
                {{ $purchase->supplier->name ?? 'N/A' }}<br>
                {{ $purchase->supplier->phone ?? '' }}
            </div>

            <div class="col-md-4">
                <strong>Entry Date:</strong><br>
                {{ $purchase->created_at 
                    ? $purchase->created_at->format('d-m-Y H:i') 
                    : '' }}
            </div>

            <div class="col-md-4 text-end">
                <strong>Total Items:</strong><br>
                {{ $purchase->items->count() }}
            </div>
        </div>

        {{-- ITEMS --}}
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>#</th>
                        <th>Medicine</th>
                        <th>Batch</th>
                        <th>Expiry</th>
                        <th>Qty</th>
                        <th>Free</th>
                        <th>MRP</th>
                        <th>Rate</th>
                        <th>GST %</th>
                        <th>Amount</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($purchase->items as $index => $item)

                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>

                            {{-- ITEM --}}
                            <td>{{ $item->item->name ?? 'N/A' }}</td>

                            {{-- BATCH --}}
                            <td class="text-center">
                                {{ $item->batch->batch_code ?? '-' }}
                            </td>

                            {{-- EXPIRY --}}
                            <td class="text-center">
                                {{ $item->batch && $item->batch->expiry_date 
                                    ? \Carbon\Carbon::parse($item->batch->expiry_date)->format('m/Y') 
                                    : '-' }}
                            </td>

                            {{-- QTY --}}
                            <td class="text-center">{{ $item->quantity }}</td>

                            {{-- FREE --}}
                            <td class="text-center">{{ $item->free_quantity ?? 0 }}</td>

                            {{-- MRP --}}
                            <td class="text-end">
                                ₹ {{ number_format($item->mrp, 2) }}
                            </td>

                            {{-- ✅ FIX RATE --}}
                            <td class="text-end">
                                ₹ {{ number_format($item->ptr ?? 0, 2) }}
                            </td>

                            {{-- GST --}}
                            <td class="text-center">
                                {{ $item->gst_percent ?? 0 }}%
                            </td>

                            {{-- ✅ FIX AMOUNT --}}
                            <td class="text-end">
                                ₹ {{ number_format($item->total_amount ?? 0, 2) }}
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-danger">
                                No items found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- TOTALS --}}
        <div class="row mt-4">

            <div class="col-md-6"></div>

            <div class="col-md-6">
                <table class="table table-bordered">

                    <tr>
                        <th>Sub Total</th>
                        <td class="text-end">
                            ₹ {{ number_format($purchase->total_amount ?? 0, 2) }}
                        </td>
                    </tr>

                    <tr>
                        <th>Discount</th>
                        <td class="text-end text-danger">
                            ₹ {{ number_format($purchase->total_discount ?? 0, 2) }}
                        </td>
                    </tr>

                    <tr>
                        <th>GST Amount</th>
                        <td class="text-end">
                            ₹ {{ number_format($purchase->total_gst ?? 0, 2) }}
                        </td>
                    </tr>

                    <tr class="table-dark">
                        <th>Net Amount</th>
                        <th class="text-end">
                            ₹ {{ number_format($purchase->net_amount ?? 0, 2) }}
                        </th>
                    </tr>

                </table>
            </div>

        </div>

        {{-- BUTTONS --}}
        <div class="mt-3 d-flex justify-content-end gap-2 no-print">

            <a href="{{ route('purchase.index') }}" class="btn btn-secondary">
                Back
            </a>

            <button onclick="window.print()" class="btn btn-primary">
                Print
            </button>

        </div>

    </div>
</div>

</div>

@endsection