@extends('supplier.layouts.app')

@section('title', 'Catalog Details - ' . ($catalog->item->name ?? 'Product'))

@section('content')
    <style>
        /* Catalog Details Page Styles */
        .page-header {
            background: white;
            border-radius: 20px;
            padding: 24px 28px;
            margin-bottom: 28px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid #eef2ff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 6px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title h3 i {
            color: #0ea5e9;
            font-size: 1.6rem;
        }

        .page-title p {
            color: #64748b;
            font-size: 0.85rem;
            margin: 0;
        }

        .btn-back {
            background: #f1f5f9;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
            color: #475569;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back:hover {
            background: #e2e8f0;
            color: #0f172a;
            transform: translateX(-2px);
        }

        .btn-edit {
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            border: none;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
            color: white;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #0284c7, #2563eb);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
            color: white;
        }

        /* Details Card */
        .details-container {
            background: white;
            border-radius: 24px;
            border: 1px solid #eef2ff;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        /* Product Hero Section */
        .product-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 32px;
            color: white;
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .product-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }

        .product-info h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 0 8px 0;
        }

        .product-info .product-meta {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            font-size: 0.85rem;
            opacity: 0.8;
        }

        .stock-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Details Grid */
        .details-section {
            padding: 28px 32px;
            border-bottom: 1px solid #f1f5f9;
        }

        .details-section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 12px;
            border-bottom: 2px solid #eef2ff;
        }

        .section-title i {
            color: #0ea5e9;
            font-size: 1.1rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .info-row {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .info-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-label i {
            color: #0ea5e9;
            font-size: 0.7rem;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: #0f172a;
        }

        .info-value.price {
            font-size: 1.2rem;
            color: #0ea5e9;
        }

        .info-value.stock {
            font-size: 1.2rem;
        }

        .stock-high {
            color: #10b981;
        }

        .stock-medium {
            color: #eab308;
        }

        .stock-low {
            color: #ef4444;
        }

        /* Pricing Cards */
        .pricing-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .price-card {
            background: #f8fafc;
            border-radius: 16px;
            padding: 16px;
            text-align: center;
            border: 1px solid #eef2ff;
            transition: all 0.2s ease;
        }

        .price-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .price-card .price-label {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .price-card .price-value {
            font-size: 1.3rem;
            font-weight: 700;
            color: #0f172a;
        }

        .price-card .price-value small {
            font-size: 0.7rem;
            font-weight: normal;
        }

        .margin-card {
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            color: white;
        }

        .margin-card .price-label {
            color: rgba(255, 255, 255, 0.8);
        }

        .margin-card .price-value {
            color: white;
        }

        /* Price Flow */
        .price-flow {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .price-node {
            flex: 1;
            text-align: center;
            padding: 12px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .price-node-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 6px;
        }

        .price-node-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
        }

        .price-arrow {
            font-size: 1.2rem;
            color: #0ea5e9;
        }

        .margin-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .margin-high {
            background: #dcfce7;
            color: #15803d;
        }

        .margin-medium {
            background: #fef3c7;
            color: #b45309;
        }

        .margin-low {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* Stock History Table */
        .stock-history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .stock-history-table th {
            padding: 12px 16px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        .stock-history-table td {
            padding: 12px 16px;
            font-size: 0.85rem;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
        }

        .stock-history-table tbody tr:hover {
            background: #f8fafc;
        }

        .badge-type {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .badge-purchase {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-sale {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-inbound {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-outbound {
            background: #fef3c7;
            color: #b45309;
        }

        .qty-positive {
            color: #10b981;
            font-weight: 700;
        }

        .qty-negative {
            color: #ef4444;
            font-weight: 700;
        }

        /* Action Buttons */
        .action-buttons {
            padding: 24px 32px;
            background: #fafcff;
            display: flex;
            justify-content: flex-end;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-outline-secondary-custom {
            background: white;
            border: 1.5px solid #e2e8f0;
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 0.85rem;
            color: #475569;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-outline-secondary-custom:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .btn-danger-custom {
            background: #ef4444;
            border: none;
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 0.85rem;
            color: white;
            transition: all 0.2s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-danger-custom:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 20px;
            }

            .product-hero {
                padding: 24px;
                flex-direction: column;
                text-align: center;
            }

            .product-info h2 {
                font-size: 1.4rem;
            }

            .details-section {
                padding: 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .pricing-cards {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .price-flow {
                flex-direction: column;
            }

            .price-arrow {
                transform: rotate(90deg);
            }

            .action-buttons {
                padding: 20px;
                flex-direction: column-reverse;
            }

            .btn-outline-secondary-custom,
            .btn-danger-custom,
            .btn-edit {
                justify-content: center;
            }

            .stock-history-table {
                min-width: 500px;
            }

            .table-responsive {
                overflow-x: auto;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .details-container {
            animation: fadeIn 0.3s ease;
        }
    </style>

    <div class="page-header">
        <div class="page-title">
            <h3>
                <i class="fa fa-info-circle"></i>
                Catalog Details
            </h3>
            <p>View complete product catalog information</p>
        </div>
        <a href="{{ route('supplier.catalogs.index') }}" class="btn-back">
            <i class="fa fa-arrow-left"></i> Back to Catalogs
        </a>
    </div>

    <div class="details-container">
        <!-- Product Hero Section -->
        <div class="product-hero">
            <div class="product-icon">
                <i class="fa fa-capsules"></i>
            </div>
            <div class="product-info">
                <h2>{{ $catalog->item->name ?? 'N/A' }}</h2>
                <div class="product-meta">
                    <span><i class="fa fa-barcode"></i> Batch: {{ $catalog->batch_no }}</span>
                    @if($catalog->item->generic_name ?? false)
                        <span><i class="fa fa-flask"></i> {{ $catalog->item->generic_name }}</span>
                    @endif
                    <span class="stock-badge">
                        <i class="fa fa-cubes"></i> Stock: {{ $catalog->current_stock ?? 0 }} units
                    </span>
                </div>
            </div>
        </div>

        <!-- Basic Information Section -->
        <div class="details-section">
            <div class="section-title">
                <i class="fa fa-info-circle"></i>
                Basic Information
            </div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">
                        <i class="fa fa-tag"></i> Batch Number
                    </div>
                    <div class="info-value">
                        <span class="font-monospace">{{ $catalog->batch_no }}</span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">
                        <i class="fa fa-calendar"></i> Expiry Date
                    </div>
                    <div class="info-value">
                        @php
                            $expiryDate = $catalog->expiry_date ? \Carbon\Carbon::parse($catalog->expiry_date) : null;
                            $isExpired = $expiryDate && $expiryDate->isPast();
                            $isExpiringSoon = $expiryDate && !$isExpired && $expiryDate->diffInDays(now()) <= 90;
                        @endphp
                        <span
                            class="{{ $isExpired ? 'text-danger' : ($isExpiringSoon ? 'text-warning' : 'text-success') }}">
                            {{ $catalog->expiry_date ?? 'N/A' }}
                            @if($isExpired)
                                <i class="fa fa-exclamation-triangle ms-1"></i> (Expired)
                            @elseif($isExpiringSoon)
                                <i class="fa fa-clock ms-1"></i> (Expiring soon)
                            @endif
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">
                        <i class="fa fa-building"></i> Brand
                    </div>
                    <div class="info-value">
                        {{ $catalog->item->brand ?? 'N/A' }}
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">
                        <i class="fa fa-cubes"></i> Current Stock
                    </div>
                    <div class="info-value stock">
                        @php
                            $stock = $catalog->current_stock ?? 0;
                            $stockClass = $stock > 100 ? 'stock-high' : ($stock > 20 ? 'stock-medium' : 'stock-low');
                        @endphp
                        <span class="{{ $stockClass }} fw-bold">
                            {{ $stock }} units
                        </span>
                        @if($stock <= 20 && $stock > 0)
                            <span class="badge bg-warning text-dark ms-2">Low Stock</span>
                        @elseif($stock <= 0)
                            <span class="badge bg-danger ms-2">Out of Stock</span>
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">
                        <i class="fa fa-check-circle"></i> Status
                    </div>
                    <div class="info-value">
                        @if($catalog->is_active && !$isExpired)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Information Section -->
        <div class="details-section">
            <div class="section-title">
                <i class="fa fa-rupee-sign"></i>
                Pricing Information
            </div>

            <div class="pricing-cards">
                <div class="price-card">
                    <div class="price-label">
                        <i class="fa fa-chart-line"></i> Purchase Price
                    </div>
                    <div class="price-value">
                        ₹{{ number_format($catalog->purchase_price ?? 0, 2) }}
                    </div>
                    <small class="text-muted">Your cost from supplier</small>
                </div>

                <div class="price-card">
                    <div class="price-label">
                        <i class="fa fa-calculator"></i> Base Price
                    </div>
                    <div class="price-value">
                        ₹{{ number_format($catalog->base_price ?? 0, 2) }}
                    </div>
                    <small class="text-muted">System base reference price</small>
                </div>

                <div class="price-card">
                    <div class="price-label">
                        <i class="fa fa-store"></i> Retailer Price
                    </div>
                    <div class="price-value">
                        ₹{{ number_format($catalog->retailer_price ?? 0, 2) }}
                    </div>
                    <small class="text-muted">Selling price to retailers</small>
                </div>

                <div class="price-card">
                    <div class="price-label">
                        <i class="fa fa-money-bill"></i> MRP
                    </div>
                    <div class="price-value">
                        ₹{{ number_format($catalog->retailer_mrp ?? 0, 2) }}
                    </div>
                    <small class="text-muted">Maximum retail price</small>
                </div>
            </div>

            <!-- Price Flow Visualization -->
            @php
                $purchase = $catalog->purchase_price ?? 0;
                $base = $catalog->base_price ?? 0;
                $retailer = $catalog->retailer_price ?? 0;
                $mrp = $catalog->retailer_mrp ?? 0;

                $marginPurchaseToBase = $purchase > 0 && $base > 0 ? (($base - $purchase) / $purchase) * 100 : 0;
                $marginBaseToRetailer = $base > 0 && $retailer > 0 ? (($retailer - $base) / $base) * 100 : 0;
                $totalMargin = $purchase > 0 && $retailer > 0 ? (($retailer - $purchase) / $purchase) * 100 : 0;
                $discountToMrp = $mrp > 0 && $retailer > 0 ? (($mrp - $retailer) / $mrp) * 100 : 0;

                $marginClass = $totalMargin >= 20 ? 'margin-high' : ($totalMargin >= 10 ? 'margin-medium' : 'margin-low');
            @endphp



            <div style="margin-top: 20px; text-align: center;">
                <span class="margin-badge {{ $marginClass }}" style="font-size: 0.85rem; padding: 8px 20px;">
                    <i class="fa fa-percent"></i> Total Margin: {{ number_format($totalMargin, 1) }}%
                </span>
                <span class="margin-badge" style="background:#e0f2fe; color:#0369a1; margin-left: 10px;">
                    <i class="fa fa-chart-line"></i> Est. Profit on Stock:
                    ₹{{ number_format(($retailer - $purchase) * $stock, 2) }}
                </span>
            </div>
        </div>
@php
    $totalPurchase = $catalog->stocks
        ->whereIn('type', ['purchase','inbound'])
        ->sum('qty');

    $totalSale = $catalog->stocks
        ->whereIn('type', ['sale','outbound'])
        ->sum('qty');

    $currentStock = $catalog->current_stock ?? 0;
@endphp<div class="details-section">
    <div class="section-title">
        <i class="fa fa-chart-bar"></i>
        Stock Summary
    </div>

    <div class="pricing-cards">

        <!-- TOTAL PURCHASE -->
        <div class="price-card">
            <div class="price-label">
                <i class="fa fa-arrow-down"></i> Total Stock In
            </div>
            <div class="price-value text-success">
                +{{ $totalPurchase }}
            </div>
            <small class="text-muted">Total purchased stock</small>
        </div>

        <!-- TOTAL SALE -->
        <div class="price-card">
            <div class="price-label">
                <i class="fa fa-arrow-up"></i> Total Sold
            </div>
            <div class="price-value text-danger">
                -{{ $totalSale }}
            </div>
            <small class="text-muted">Total sold stock</small>
        </div>

        <!-- CURRENT -->
        <div class="price-card">
            <div class="price-label">
                <i class="fa fa-cubes"></i> Current Stock
            </div>
            <div class="price-value">
                {{ $currentStock }}
            </div>
            <small class="text-muted">Available now</small>
        </div>

        <!-- NET -->
        <div class="price-card">
            <div class="price-label">
                <i class="fa fa-balance-scale"></i> Net Movement
            </div>
            <div class="price-value">
                {{ $totalPurchase - $totalSale }}
            </div>
            <small class="text-muted">Calculated stock</small>
        </div>

    </div>
</div>
        <!-- Stock History Section -->
        <div class="details-section">
            <div class="section-title">
                <i class="fa fa-history"></i>
                Stock Movement History
            </div>

            <div class="table-responsive">
                <table class="stock-history-table">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Reference</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($catalog->stocks->sortByDesc('created_at') as $stock)
                            <tr>
                                <td style="font-size: 0.75rem; color:#64748b;">
                                    <i class="fa fa-calendar-alt"></i> {{ $stock->created_at->format('d M, Y') }}<br>
                                    <i class="fa fa-clock"></i> {{ $stock->created_at->format('h:i A') }}
                                </td>
                                <td>
                                    @if($stock->type == 'purchase')
                                        <span class="badge-type badge-purchase">
                                            <i class="fa fa-arrow-down"></i> Purchase
                                        </span>
                                    @elseif($stock->type == 'sale')
                                        <span class="badge-type badge-sale">
                                            <i class="fa fa-arrow-up"></i> Sale
                                        </span>
                                    @elseif($stock->type == 'inbound')
                                        <span class="badge-type badge-inbound">
                                            <i class="fa fa-arrow-down"></i> Stock In
                                        </span>
                                    @elseif($stock->type == 'outbound')
                                        <span class="badge-type badge-outbound">
                                            <i class="fa fa-arrow-up"></i> Stock Out
                                        </span>
                                    @else
                                        <span class="badge-type" style="background:#f1f5f9; color:#475569;">
                                            <i class="fa fa-edit"></i> {{ ucfirst($stock->type) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="{{ in_array($stock->type, ['purchase', 'inbound']) ? 'qty-positive' : 'qty-negative' }}">
                                        {{ in_array($stock->type, ['purchase', 'inbound']) ? '+' : '-' }}{{ $stock->qty }}
                                    </span>
                                </td>
                                <td>
                                    @if($stock->reference_id)
                                        <span class="badge bg-secondary">#{{ $stock->reference_id }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size: 0.8rem;">{{ $stock->note ?? '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fa fa-chart-line fa-2x mb-2 d-block"></i>
                                    No stock history found for this catalog
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tax Information Section -->
        @if(($catalog->gst_percent ?? 0) > 0 || ($catalog->hsn_code ?? false) || ($catalog->item->gst_percent ?? false))
            <div class="details-section">
                <div class="section-title">
                    <i class="fa fa-percent"></i>
                    Tax Information
                </div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fa fa-percent"></i> GST Percentage
                        </div>
                        <div class="info-value">
                            @php $gst = $catalog->gst_percent ?? $catalog->item->gst_percent ?? 0; @endphp
                            <span class="badge" style="background:#e0f2fe; color:#0284c7; padding: 6px 14px;">
                                {{ $gst }}%
                            </span>
                            @if($gst == 0)
                                (Nil Rated)
                            @elseif($gst == 5)
                                (Essential Medicines)
                            @elseif($gst == 12)
                                (Standard Rate)
                            @elseif($gst == 18)
                                (Standard Rate)
                            @elseif($gst == 28)
                                (Luxury/Sin Goods)
                            @endif
                        </div>
                    </div>
                    @if($catalog->hsn_code ?? false)
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fa fa-hashtag"></i> HSN Code
                            </div>
                            <div class="info-value">
                                <span class="font-monospace">{{ $catalog->hsn_code }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Additional Info Section -->
        @if($catalog->description ?? false)
            <div class="details-section">
                <div class="section-title">
                    <i class="fa fa-file-alt"></i>
                    Description
                </div>
                <div class="info-value" style="line-height: 1.6;">
                    {{ $catalog->description }}
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('supplier.catalogs.edit', $catalog->id) }}" class="btn-edit">
                <i class="fa fa-pencil-alt"></i> Edit Catalog
            </a>
            <button type="button" class="btn-danger-custom" onclick="confirmDelete({{ $catalog->id }})">
                <i class="fa fa-trash-alt"></i> Delete Catalog
            </button>
            <a href="{{ route('supplier.catalogs.index') }}" class="btn-outline-secondary-custom">
                <i class="fa fa-times"></i> Close
            </a>
        </div>
    </div>

    <!-- Delete Confirmation Form -->
    <form id="deleteForm" method="POST" action="{{ route('supplier.catalogs.destroy', $catalog->id) }}"
        style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDelete(catalogId) {
            if (confirm('Are you sure you want to delete this catalog? This action cannot be undone.')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>

@endsection