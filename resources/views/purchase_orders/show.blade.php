@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    
    {{-- Print Button (Hidden in Print) --}}
    <div class="d-print-none mb-4 d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Purchase Orders
            </a>
        </div>
        <button class="btn btn-primary btn-lg shadow-sm" onclick="window.print()">
            <i class="fas fa-print me-2"></i>Print Invoice
        </button>
    </div>

    {{-- INVOICE DOCUMENT --}}
    <div class="invoice-container">
        
        {{-- Watermark --}}
        <div class="watermark">PharmaSphere 360</div>

        {{-- Invoice Header --}}
        <div class="invoice-header">
            <div class="row align-items-start">
                <div class="col-md-6">
                    <div class="company-info">
                        <h1 class="company-name">PharmaSphere 360</h1>
                        <div class="company-details">
                            <p class="mb-1">123 Healthcare Avenue, MedTech Park</p>
                            <p class="mb-1">Mumbai, Maharashtra - 400001</p>
                            <p class="mb-1">GSTIN: 27ABCDE1234F1Z5 | CIN: U85100MH2020PTC123456</p>
                            <p class="mb-0">Phone: +91 22 1234 5678 | Email: info@pharmasphere360.com</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="invoice-title">
                        <h2 class="text-uppercase fw-bold text-danger mb-2">Purchase Invoice</h2>
                        <div class="invoice-meta">
                            <div class="meta-item">
                                <span class="label">Invoice No:</span>
                                <span class="value fw-bold">{{ $po->order_number }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="label">Invoice Date:</span>
                                <span class="value">{{ date('d-m-Y', strtotime($po->order_date)) }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="label">Entry Time:</span>
                                <span class="value">{{ date('h:i A', strtotime($po->created_at ?? $po->order_date)) }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="label">Status:</span>
                                <span class="status-badge 
                                    @if($po->status == 'pending') status-pending
                                    @elseif($po->status == 'confirmed') status-confirmed
                                    @elseif($po->status == 'processing') status-processing
                                    @elseif($po->status == 'dispatched') status-dispatched
                                    @elseif($po->status == 'delivered') status-delivered
                                    @else status-default
                                    @endif">
                                    {{ ucfirst($po->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Supplier & Shipping Info --}}
        <div class="info-section">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="info-card">
                        <h3 class="info-title">Supplier Details (Bill From)</h3>
                        <div class="info-content">
                            <h4 class="supplier-name">{{ $po->supplier->name ?? 'Not Available' }}</h4>
                            <div class="info-grid">
                                <div class="info-row">
                                    <span class="info-label">Supplier Code:</span>
                                    <span class="info-value">{{ $po->supplier->supplier_code ?? '-' }}</span>
                                </div>
                                @if(optional($po->supplier)->phone)
                                <div class="info-row">
                                    <span class="info-label">Phone:</span>
                                    <span class="info-value">{{ $po->supplier->phone }}</span>
                                </div>
                                @endif
                                @if(optional($po->supplier)->email)
                                <div class="info-row">
                                    <span class="info-label">Email:</span>
                                    <span class="info-value">{{ $po->supplier->email }}</span>
                                </div>
                                @endif
                                @if(optional($po->supplier)->address)
                                <div class="info-row">
                                    <span class="info-label">Address:</span>
                                    <span class="info-value">{{ $po->supplier->address }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card">
                        <h3 class="info-title">Shipping To (Bill To)</h3>
                        <div class="info-content">
                            <h4 class="supplier-name">PharmaSphere 360</h4>
                            <div class="info-grid">
                                <div class="info-row">
                                    <span class="info-label">Warehouse:</span>
                                    <span class="info-value">Central Warehouse - Block A</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Address:</span>
                                    <span class="info-value">123 Healthcare Avenue, MedTech Park, Mumbai - 400001</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">GSTIN:</span>
                                    <span class="info-value">27ABCDE1234F1Z5</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="items-section">
            <h3 class="section-title">Order Items <span class="item-count">(Total Items: {{ $po->items->count() }})</span></h3>
            <div class="table-responsive">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Medicine Name</th>
                            <th class="text-center">Batch</th>
                            <th class="text-center">Expiry</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Free</th>
                            <th class="text-end">MRP (₹)</th>
                            <th class="text-end">PTR (₹)</th>
                            <th class="text-center">GST %</th>
                            <th class="text-end">Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        
                        @forelse($po->items as $key => $item)
@php
    $amount = $item->total_amount ?? 0;
    $total += $amount;

    // Batch tera auto (correct)

// batch fetch from DB
$batch = null;

/*
|--------------------------------------------------------------------------
| Batch should show after Publish for Sale
|--------------------------------------------------------------------------
|
| Old delivered logic removed
| New logic:
| dispatched + published_for_sale = 1
|
*/

if (
    $po->status === 'dispatched'
    && $po->stock_received == 1
    && $po->price_updated == 1
    && $po->published_for_sale == 1
) {
    $batch = \App\Models\Batch::where('item_id', $item->item_id)
        ->latest()
        ->first();
}

/*
|--------------------------------------------------------------------------
| Batch Number
|--------------------------------------------------------------------------
*/

$batchNo = $batch
    ? $batch->batch_code
    : 'Pending Publish For Sale';

/*
|--------------------------------------------------------------------------
| Expiry Date
|--------------------------------------------------------------------------
*/

$expiryDate = ($batch && $batch->expiry_date)
    ? \Carbon\Carbon::parse($batch->expiry_date)->format('m/Y')
    : '-';@endphp
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td class="fw-medium">{{ $item->item->name ?? 'Unknown Item' }}</td>
                                <td class="text-center">{{ $batchNo }}</td>
                                <td class="text-center">{{ $expiryDate }}</td>
                                <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                                <td class="text-end">{{ number_format($item->free_quantity ?? 0, 2) }}</td>
                            <td class="text-end">
    ₹{{ number_format(
        $item->final_mrp
        ?? optional($item->catalog)->base_price
        ?? 0,
        2
    ) }}
</td>

<td class="text-end">
    ₹{{ number_format(
        optional($item->catalog)->retailer_price
        ?? $item->rate
        ?? 0,
        2
    ) }}
</td>
                                <td class="text-center">{{ number_format($item->gst_percent, 2) }}%</td>
                                <td class="text-end fw-semibold">₹{{ number_format($amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">No items found in this purchase order</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Totals Section --}}
        <div class="totals-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="payment-info">
                        <h4 class="info-title-small">Payment Information</h4>
                        <p class="mb-1">Payment Terms: Net 30 Days</p>
                        <p class="mb-1">Due Date: {{ date('d-m-Y', strtotime($po->order_date . ' +30 days')) }}</p>
                        <p class="mb-0">Bank: HDFC Bank | A/C: 12345678901234 | IFSC: HDFC0001234</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="totals-box">
                        <div class="total-row">
                            <span class="total-label">Subtotal:</span>
                            <span class="total-value">₹{{ number_format($po->total_amount ?? 0, 2) }}</span>
                        </div>
                        <div class="total-row">
                            <span class="total-label">Discount:</span>
                            <span class="total-value text-success">- ₹{{ number_format($po->total_discount ?? 0, 2) }}</span>
                        </div>
                        <div class="total-row">
                            <span class="total-label">GST Amount:</span>
                            <span class="total-value">₹{{ number_format($po->total_gst ?? 0, 2) }}</span>
                        </div>
                        <div class="total-row grand-total">
                            <span class="total-label">Grand Total:</span>
                            <span class="total-value">₹{{ number_format($po->net_amount ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Amount in Words --}}
        <div class="amount-words">
            <strong>Amount in Words:</strong> 
            {{ ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($po->net_amount ?? 0)) }} Rupees Only
        </div>

        {{-- Footer --}}
        <div class="invoice-footer">
            <div class="row align-items-end">
                <div class="col-md-8">
                    <p class="terms mb-1"><strong>Terms & Conditions:</strong></p>
                    <ol class="terms-list">
                        <li>Goods once sold will not be taken back or exchanged.</li>
                        <li>All disputes subject to Mumbai jurisdiction only.</li>
                        <li>Interest @ 24% per annum will be charged on overdue payments.</li>
                    </ol>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="signature-area">
                        <p class="mb-4">For PharmaSphere 360</p>
                        <div class="signature-line"></div>
                        <p class="authorized-text">Authorized Signatory</p>
                    </div>
                </div>
            </div>
            <div class="system-footer">
                <p class="mb-0">This is a computer generated invoice - No signature required</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Reset and Base Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: #f5f7fa;
        line-height: 1.5;
    }

    /* Invoice Container */
    .invoice-container {
        max-width: 1200px;
        margin: 0 auto;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        padding: 40px;
        position: relative;
        overflow: hidden;
    }

    /* Watermark */
    .watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 140px;
        font-weight: 900;
        color: rgba(220, 53, 69, 0.03);
        pointer-events: none;
        white-space: nowrap;
        z-index: 1;
        font-family: 'Arial Black', sans-serif;
        text-transform: uppercase;
        letter-spacing: 10px;
    }

    /* Company Header */
    .company-name {
        font-size: 32px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 10px;
        letter-spacing: -0.5px;
    }

    .company-details {
        color: #666;
        font-size: 13px;
        line-height: 1.6;
    }

    /* Invoice Title */
    .invoice-title h2 {
        font-size: 28px;
        letter-spacing: 2px;
        margin-bottom: 15px;
    }

    .invoice-meta {
        background: #f8f9fa;
        padding: 15px 20px;
        border-radius: 8px;
        display: inline-block;
        text-align: left;
    }

    .meta-item {
        margin-bottom: 8px;
    }

    .meta-item:last-child {
        margin-bottom: 0;
    }

    .meta-item .label {
        font-size: 12px;
        color: #666;
        display: inline-block;
        width: 90px;
    }

    .meta-item .value {
        font-size: 14px;
        color: #1a1a1a;
        font-weight: 500;
    }

    /* Status Badges */
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pending { background: #fff3cd; color: #856404; }
    .status-confirmed { background: #cce5ff; color: #004085; }
    .status-processing { background: #d4edda; color: #155724; }
    .status-dispatched { background: #d1ecf1; color: #0c5460; }
    .status-delivered { background: #d4edda; color: #155724; }
    .status-default { background: #e2e3e5; color: #383d41; }

    /* Info Cards */
    .info-section {
        margin: 30px 0;
    }

    .info-card {
        background: #fafbfc;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 20px;
        height: 100%;
    }

    .info-title {
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6c757d;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .supplier-name {
        font-size: 18px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 15px;
    }

    .info-grid {
        display: grid;
        gap: 10px;
    }

    .info-row {
        display: grid;
        grid-template-columns: 100px 1fr;
        font-size: 13px;
    }

    .info-label {
        color: #6c757d;
        font-weight: 500;
    }

    .info-value {
        color: #1a1a1a;
        font-weight: 500;
    }

    /* Items Table */
    .items-section {
        margin: 30px 0;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .item-count {
        font-size: 13px;
        color: #6c757d;
        font-weight: 400;
        text-transform: none;
        margin-left: 10px;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .items-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .items-table th {
        padding: 12px 8px;
        font-weight: 600;
        color: #495057;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .items-table td {
        padding: 12px 8px;
        border-bottom: 1px solid #e9ecef;
    }

    .items-table tbody tr:hover {
        background: #f8f9fa;
    }

    .items-table tbody tr:last-child td {
        border-bottom: 2px solid #dee2e6;
    }

    /* Totals Section */
    .totals-section {
        margin: 30px 0 20px;
    }

    .payment-info {
        background: #f8f9fa;
        padding: 15px 20px;
        border-radius: 8px;
        font-size: 13px;
    }

    .info-title-small {
        font-size: 13px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .totals-box {
        background: #f8f9fa;
        padding: 15px 20px;
        border-radius: 8px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 14px;
    }

    .total-row:not(:last-child) {
        border-bottom: 1px solid #dee2e6;
    }

    .total-label {
        color: #6c757d;
        font-weight: 500;
    }

    .total-value {
        font-weight: 600;
        color: #1a1a1a;
    }

    .grand-total {
        margin-top: 5px;
        padding-top: 12px !important;
        border-top: 2px solid #dc3545 !important;
        border-bottom: none !important;
    }

    .grand-total .total-label {
        font-size: 16px;
        font-weight: 700;
        color: #dc3545;
    }

    .grand-total .total-value {
        font-size: 20px;
        font-weight: 700;
        color: #dc3545;
    }

    /* Amount in Words */
    .amount-words {
        margin: 20px 0;
        padding: 12px 15px;
        background: #f8f9fa;
        border-radius: 6px;
        font-size: 13px;
        color: #495057;
    }

    /* Footer */
    .invoice-footer {
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }

    .terms-list {
        margin: 0;
        padding-left: 20px;
        font-size: 12px;
        color: #6c757d;
    }

    .terms-list li {
        margin-bottom: 3px;
    }

    .signature-area {
        padding-top: 20px;
    }

    .signature-line {
        width: 200px;
        height: 1px;
        background: #1a1a1a;
        margin: 10px 0 5px;
        margin-left: auto;
    }

    .authorized-text {
        font-size: 12px;
        color: #6c757d;
    }

    .system-footer {
        margin-top: 30px;
        text-align: center;
        font-size: 11px;
        color: #adb5bd;
    }

    /* Print Styles */
    @media print {
        body {
            background: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .container-fluid {
            padding: 0 !important;
        }

        .d-print-none {
            display: none !important;
        }

        .invoice-container {
            box-shadow: none !important;
            padding: 20px !important;
            border-radius: 0 !important;
        }

        .watermark {
            opacity: 0.05 !important;
            color: #000 !important;
        }

        .info-card {
            background: #f8f9fa !important;
            border: 1px solid #dee2e6 !important;
        }

        .items-table thead {
            background: #f8f9fa !important;
        }

        .status-badge {
            border: 1px solid currentColor !important;
        }

        .totals-box, .payment-info {
            background: #f8f9fa !important;
        }

        .grand-total {
            border-top-color: #000 !important;
        }

        .grand-total .total-label,
        .grand-total .total-value {
            color: #000 !important;
        }

        a, button, .btn {
            display: none !important;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .invoice-container {
            padding: 20px;
        }

        .company-name {
            font-size: 24px;
        }

        .invoice-title h2 {
            font-size: 22px;
        }

        .info-row {
            grid-template-columns: 1fr;
            gap: 2px;
        }

        .items-table {
            font-size: 11px;
        }

        .items-table th,
        .items-table td {
            padding: 8px 4px;
        }

        .watermark {
            font-size: 80px;
        }

        .grand-total .total-value {
            font-size: 18px;
        }
    }
</style>

{{-- Font Awesome for Icons (if not already included) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

{{-- Google Fonts for better typography --}}
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

@endsection