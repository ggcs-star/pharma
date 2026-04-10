@extends('supplier.layouts.app')

@section('content')
    <style>
        /* Page Header Styles */
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

        .page-title h4 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 6px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title h4 i {
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

        /* Info Card */
        .info-card {
            background: white;
            border-radius: 24px;
            border: 1px solid #eef2ff;
            overflow: hidden;
            margin-bottom: 28px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .card-header-custom {
            padding: 20px 28px;
            background: #fafcff;
            border-bottom: 1px solid #eef2ff;
        }

        .card-header-custom h5 {
            font-size: 0.9rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header-custom h5 i {
            color: #0ea5e9;
        }

        .card-body-custom {
            padding: 28px;
        }

        /* Product Image */
        .product-image-container {
            background: #f8fafc;
            border-radius: 20px;
            padding: 16px;
            text-align: center;
            border: 1px solid #eef2ff;
        }

        .product-image {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 12px;
        }

        .product-image-placeholder {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: #94a3b8;
        }

        .product-image-placeholder i {
            font-size: 3rem;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }

        .info-item {
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-label i {
            color: #0ea5e9;
            font-size: 0.75rem;
        }

        .info-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: #0f172a;
        }

        .info-value .badge-custom {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Batch Table */
        .batch-table-container {
            background: white;
            border-radius: 24px;
            border: 1px solid #eef2ff;
            overflow: hidden;
            margin-top: 28px;
        }

        .batch-table {
            width: 100%;
            border-collapse: collapse;
        }

        .batch-table thead tr {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .batch-table th {
            padding: 16px 16px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #475569;
            text-align: left;
        }

        .batch-table td {
            padding: 16px 16px;
            font-size: 0.85rem;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .batch-table tbody tr:hover {
            background: #f8fafc;
        }

        /* Batch Row Expanded */
        .batch-expanded-row {
            background: #fafcff;
        }

        .batch-expanded-row td {
            padding: 20px 24px;
        }

        .stock-history {
            background: #f8fafc;
            border-radius: 16px;
            padding: 16px;
            margin-top: 8px;
        }

        .stock-history-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stock-history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .stock-history-table th {
            padding: 10px 12px;
            font-size: 0.65rem;
            font-weight: 700;
            color: #64748b;
            background: #f1f5f9;
            border-radius: 8px;
            text-align: left;
        }

        .stock-history-table td {
            padding: 10px 12px;
            font-size: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Status Badges */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .badge-active {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-expired {
            background: #fef3c7;
            color: #b45309;
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

        /* Stock Tags */
        .stock-tag {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .stock-high {
            background: #dcfce7;
            color: #15803d;
        }

        .stock-medium {
            background: #fef3c7;
            color: #b45309;
        }

        .stock-low {
            background: #fee2e2;
            color: #b91c1c;
        }

        .profit-positive {
            color: #10b981;
            font-weight: 700;
        }

        .profit-negative {
            color: #ef4444;
            font-weight: 700;
        }

        /* Summary Cards */
        .summary-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .summary-card {
            padding: 15px 20px;
            border-radius: 12px;
            min-width: 150px;
            flex: 1;
        }

        .summary-card .card-label {
            font-size: 0.7rem;
            margin-bottom: 6px;
        }

        .summary-card .card-value {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .card-inbound {
            background: #dcfce7;
        }
        .card-inbound .card-label { color: #166534; }
        .card-inbound .card-value { color: #15803d; }

        .card-outbound {
            background: #fee2e2;
        }
        .card-outbound .card-label { color: #991b1b; }
        .card-outbound .card-value { color: #dc2626; }

        .card-current {
            background: #e0f2fe;
        }
        .card-current .card-label { color: #0369a1; }
        .card-current .card-value { color: #0284c7; }

        .card-net {
            background: #f1f5f9;
        }
        .card-net .card-label { color: #475569; }
        .card-net .card-value { color: #0f172a; }

        /* Toggle Button */
        .toggle-details {
            background: none;
            border: none;
            color: #0ea5e9;
            cursor: pointer;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }

        .toggle-details:hover {
            color: #0284c7;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 12px;
        }

        .empty-state p {
            color: #94a3b8;
            font-size: 0.85rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 20px;
            }

            .card-body-custom {
                padding: 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .batch-table-container {
                overflow-x: auto;
            }

            .batch-table {
                min-width: 800px;
            }

            .summary-cards {
                flex-direction: column;
            }
        }

        /* Animation */
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

        .info-card,
        .batch-table-container {
            animation: fadeIn 0.3s ease;
        }
    </style>

    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <h4>
                    <i class="fa fa-capsules"></i>
                    Item Details
                </h4>
                <p>Complete information about {{ $item->name }}</p>
            </div>
            <a href="{{ route('supplier.items.index') }}" class="btn-back">
                <i class="fa fa-arrow-left"></i> Back to Items
            </a>
        </div>

        <!-- Item Information Card -->
        <div class="info-card">
            <div class="card-header-custom">
                <h5>
                    <i class="fa fa-info-circle"></i>
                    Product Information
                </h5>
            </div>
            <div class="card-body-custom">
                <div class="row">
                    <!-- Image Column -->
                    <div class="col-md-3 mb-4 mb-md-0">
                        <div class="product-image-container">
                            @if($item->main_image_url)
                                <img src="{{ $item->main_image_url }}" class="product-image" alt="{{ $item->name }}">
                            @else
                                <div class="product-image-placeholder">
                                    <i class="fa fa-image"></i>
                                    <span>No Image Available</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Details Column -->
                    <div class="col-md-9">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fa fa-tag"></i> Brand
                                </div>
                                <div class="info-value">{{ $item->brand ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fa fa-folder"></i> Category
                                </div>
                                <div class="info-value">
                                    <span class="badge-custom" style="background:#e0f2fe; color:#0369a1;">
                                        {{ $item->category->name ?? '-' }}
                                    </span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fa fa-folder-open"></i> Sub Category
                                </div>
                                <div class="info-value">{{ $item->subCategory->name ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fa fa-building"></i> Manufacturer
                                </div>
                                <div class="info-value">{{ $item->manufacturer->name ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fa fa-percent"></i> GST
                                </div>
                                <div class="info-value">
                                    <span class="badge-custom" style="background:#e0f2fe; color:#0284c7;">
                                        {{ $item->gst_percent ?? 0 }}%
                                    </span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fa fa-barcode"></i> HSN Code
                                </div>
                                <div class="info-value">
                                    <span style="font-family: monospace;">{{ $item->hsn_code ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fa fa-archive"></i> Rack Number
                                </div>
                                <div class="info-value">{{ $item->rack ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fa fa-qrcode"></i> Barcode
                                </div>
                                <div class="info-value">{{ $item->barcode ?? '-' }}</div>
                            </div>
                            @if($item->generic_name)
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fa fa-flask"></i> Generic Name
                                    </div>
                                    <div class="info-value">{{ $item->generic_name }}</div>
                                </div>
                            @endif
                            @if($item->description)
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fa fa-align-left"></i> Description
                                    </div>
                                    <div class="info-value">{{ $item->description }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Batch & Stock Information -->
        <div class="batch-table-container">
            <div class="card-header-custom">
                <h5>
                    <i class="fa fa-cubes"></i>
                    Batch & Stock Information
                    <span style="font-size: 0.7rem; font-weight: normal; color: #64748b; margin-left: 12px;">
                        ({{ $item->catalogs->count() }} batches)
                    </span>
                </h5>
            </div>

            @php
                // Calculate stock summary across all batches
                $totalPurchase = $item->catalogs
                    ->flatMap->stocks
                    ->whereIn('type', ['purchase', 'inbound'])
                    ->sum('qty');

                $totalSale = $item->catalogs
                    ->flatMap->stocks
                    ->whereIn('type', ['sale', 'outbound'])
                    ->sum('qty');

                $currentStock = $item->catalogs->sum('current_stock');
                $netStock = $totalPurchase - $totalSale;
            @endphp

            <!-- Stock Summary Cards -->
            <div class="summary-cards" style="padding: 0 20px 20px 20px;">
                <div class="summary-card card-inbound">
                    <div class="card-label"><i class="fa fa-arrow-down"></i> Total Stock In</div>
                    <div class="card-value">+{{ $totalPurchase }}</div>
                </div>
                <div class="summary-card card-outbound">
                    <div class="card-label"><i class="fa fa-arrow-up"></i> Total Sold</div>
                    <div class="card-value">-{{ $totalSale }}</div>
                </div>
                <div class="summary-card card-current">
                    <div class="card-label"><i class="fa fa-cubes"></i> Current Stock</div>
                    <div class="card-value">{{ $currentStock }}</div>
                </div>
                <div class="summary-card card-net">
                    <div class="card-label"><i class="fa fa-chart-line"></i> Net Movement</div>
                    <div class="card-value">{{ $netStock }}</div>
                </div>
            </div>

            @if($item->catalogs->count() > 0)
                <table class="batch-table">
                    <thead>
                        <tr>
                            <th>Batch No.</th>
                            <th>Purchase Price</th>
                            <th>Retailer Price</th>
                            <th>MRP</th>
                            <th>Current Stock</th>
                            <th>Total Stock</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->catalogs as $catalog)
                            @php
                                $currentStockBatch = $catalog->current_stock ?? 0;
                                $stockClass = $currentStockBatch > 50 ? 'stock-high' : ($currentStockBatch > 10 ? 'stock-medium' : 'stock-low');
                                $isExpired = $catalog->expiry_date && \Carbon\Carbon::parse($catalog->expiry_date)->isPast();
                                $isExpiringSoon = $catalog->expiry_date && !$isExpired && \Carbon\Carbon::parse($catalog->expiry_date)->diffInDays(now()) < 90;
                            @endphp
                            <tr id="batch-row-{{ $catalog->id }}">
                                <td>
                                    <strong style="font-family: monospace;">{{ $catalog->batch_no }}</strong>
                                </td>
                                <td>₹{{ number_format($catalog->purchase_price, 2) }}</td>
                                <td>₹{{ number_format($catalog->retailer_price, 2) }}</td>
                                <td>₹{{ number_format($catalog->retailer_mrp, 2) }}</td>
                                <td>
                                    <span class="stock-tag {{ $stockClass }}">
                                        {{ $currentStockBatch }} units
                                    </span>
                                </td>
                                <td>
   

    <small style="color:#64748b;">
        In: {{ $catalog->total_purchase ?? 0 }} |
        Out: {{ $catalog->total_sale ?? 0 }}
    </small>
</td>
                                <td>
                                    @if($catalog->expiry_date)
                                        @if($isExpired)
                                            <span class="badge-status badge-expired">
                                                <i class="fa fa-exclamation-triangle"></i>
                                                {{ \Carbon\Carbon::parse($catalog->expiry_date)->format('d M, Y') }}
                                            </span>
                                        @elseif($isExpiringSoon)
                                            <span class="badge-status" style="background:#fef3c7; color:#b45309;">
                                                <i class="fa fa-clock"></i>
                                                {{ \Carbon\Carbon::parse($catalog->expiry_date)->format('d M, Y') }}
                                            </span>
                                        @else
                                            <span style="color: #10b981;">
                                                <i class="fa fa-check-circle"></i>
                                                {{ \Carbon\Carbon::parse($catalog->expiry_date)->format('d M, Y') }}
                                            </span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($catalog->is_active && !$isExpired)
                                        <span class="badge-status badge-active">
                                            <i class="fa fa-check"></i> Active
                                        </span>
                                    @else
                                        <span class="badge-status badge-inactive">
                                            <i class="fa fa-times"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <button class="toggle-details" onclick="toggleStockHistory({{ $catalog->id }})">
                                        <i class="fa fa-chevron-down" id="toggle-icon-{{ $catalog->id }}"></i>
                                        <span id="toggle-text-{{ $catalog->id }}">Details</span>
                                    </button>
                                </td>
                            </tr>
                            <tr id="history-row-{{ $catalog->id }}" style="display: none;" class="batch-expanded-row">
                                <td colspan="9">
                                    <div class="stock-history">
                                        <div class="stock-history-title">
                                            <i class="fa fa-history"></i>
                                            Stock Movement History - Batch: {{ $catalog->batch_no }}
                                        </div>
                                        @if($catalog->stocks && $catalog->stocks->count() > 0)
                                            <table class="stock-history-table">
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>Quantity</th>
                                                        <th>Note / Reference</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($catalog->stocks->sortByDesc('created_at') as $stock)
                                                        <tr>
                                                            <td>
                                                                @if($stock->type == 'purchase')
                                                                    <span class="badge-status badge-purchase">
                                                                        <i class="fa fa-arrow-down"></i> Purchase
                                                                    </span>
                                                                @elseif($stock->type == 'sale')
                                                                    <span class="badge-status badge-sale">
                                                                        <i class="fa fa-arrow-up"></i> Sale
                                                                    </span>
                                                                @elseif($stock->type == 'inbound')
                                                                    <span class="badge-status badge-inbound">
                                                                        <i class="fa fa-arrow-down"></i> Stock In
                                                                    </span>
                                                                @elseif($stock->type == 'outbound')
                                                                    <span class="badge-status badge-outbound">
                                                                        <i class="fa fa-arrow-up"></i> Stock Out
                                                                    </span>
                                                                @else
                                                                    <span class="badge-status" style="background:#fef3c7; color:#b45309;">
                                                                        <i class="fa fa-edit"></i> {{ ucfirst($stock->type) }}
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="{{ in_array($stock->type, ['purchase', 'inbound']) ? 'profit-positive' : 'profit-negative' }}">
                                                                {{ in_array($stock->type, ['purchase', 'inbound']) ? '+' : '-' }}{{ $stock->qty }}
                                                            </td>
                                                            <td>{{ $stock->note ?? '-' }}</td>
                                                            <td style="font-size: 0.7rem; color:#64748b;">
                                                                <i class="fa fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($stock->created_at)->format('d M, Y') }}<br>
                                                                <i class="fa fa-clock"></i> {{ \Carbon\Carbon::parse($stock->created_at)->format('h:i A') }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="empty-state" style="padding: 30px;">
                                                <i class="fa fa-chart-line"></i>
                                                <p>No stock history available for this batch</p>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state" style="padding: 60px;">
                    <i class="fa fa-cubes"></i>
                    <p>No catalog/batch information available for this item</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleStockHistory(catalogId) {
            const historyRow = document.getElementById(`history-row-${catalogId}`);
            const icon = document.getElementById(`toggle-icon-${catalogId}`);
            const text = document.getElementById(`toggle-text-${catalogId}`);

            if (historyRow.style.display === 'none') {
                historyRow.style.display = 'table-row';
                icon.className = 'fa fa-chevron-up';
                text.innerHTML = 'Hide Details';
            } else {
                historyRow.style.display = 'none';
                icon.className = 'fa fa-chevron-down';
                text.innerHTML = 'Details';
            }
        }
    </script>

@endsection