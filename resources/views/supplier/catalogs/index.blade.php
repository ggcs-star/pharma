@extends('supplier.layouts.app')

@section('content')
<style>
    /* Page Header Styles */
    .page-header {
        background: white;
        border-radius: 20px;
        padding: 24px 28px;
        margin-bottom: 28px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
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

    .btn-add-catalog {
        background: linear-gradient(135deg, #0ea5e9, #3b82f6);
        border: none;
        padding: 10px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(14, 165, 233, 0.3);
    }

    .btn-add-catalog:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg, #0284c7, #2563eb);
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        border: 1px solid #eef2ff;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        border-color: #e0e7ff;
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        background: linear-gradient(135deg, #e0f2fe, #dbeafe);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon i {
        font-size: 1.6rem;
        color: #0ea5e9;
    }

    .stat-info h4 {
        font-size: 1.6rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px 0;
    }

    .stat-info p {
        font-size: 0.75rem;
        color: #64748b;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    /* Table Styles */
    .table-container {
        background: white;
        border-radius: 20px;
        border: 1px solid #eef2ff;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .catalog-table {
        width: 100%;
        border-collapse: collapse;
    }

    .catalog-table thead tr {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .catalog-table th {
        padding: 16px 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #475569;
        text-align: left;
    }

    .catalog-table td {
        padding: 16px 20px;
        font-size: 0.85rem;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .catalog-table tbody tr:hover {
        background: #fefce8;
        transition: background 0.2s ease;
    }

    /* Badge Styles */
    .badge-stock {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #dcfce7;
        color: #15803d;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-stock.low {
        background: #fee2e2;
        color: #b91c1c;
    }

    .price-tag {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.9rem;
    }

    .price-tag small {
        font-weight: 400;
        font-size: 0.7rem;
        color: #64748b;
    }

    .batch-info {
        font-family: monospace;
        font-size: 0.8rem;
        background: #f1f5f9;
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-edit {
        background: #eff6ff;
        color: #2563eb;
    }

    .btn-edit:hover {
        background: #dbeafe;
        transform: translateY(-1px);
    }

    .btn-delete {
        background: #fef2f2;
        color: #dc2626;
    }

    .btn-delete:hover {
        background: #fee2e2;
        transform: translateY(-1px);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 16px;
    }

    .empty-state h5 {
        font-size: 1.1rem;
        color: #475569;
        margin-bottom: 8px;
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
        
        .stats-grid {
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        .catalog-table th,
        .catalog-table td {
            padding: 12px 16px;
        }
        
        .action-buttons {
            flex-direction: column;
            gap: 6px;
        }
        
        .btn-action {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header">
    <div class="page-title">
        <h3>
            <i class="fa fa-book"></i> 
            Product Catalogs
        </h3>
        <p>Manage your product listings, prices, and inventory</p>
    </div>
    <a href="{{ route('supplier.catalogs.create') }}" class="btn btn-add-catalog">
        <i class="fa fa-plus me-2"></i> Add New Catalog
    </a>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa fa-cubes"></i>
        </div>
        <div class="stat-info">
            <h4>{{ $catalogs->count() }}</h4>
            <p>Total Products</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa fa-chart-line"></i>
        </div>
        <div class="stat-info">
            <h4>{{ $catalogs->sum('current_stock') }}</h4>
            <p>Total Stock</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa fa-tag"></i>
        </div>
        <div class="stat-info">
            <h4>₹{{ number_format($catalogs->avg('retailer_price') ?? 0, 2) }}</h4>
            <p>Avg. Price</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa fa-clock"></i>
        </div>
        <div class="stat-info">
            <h4>{{ $catalogs->where('expiry_date', '>', now())->count() }}</h4>
            <p>Active Listings</p>
        </div>
    </div>
</div>

<!-- Table -->
<div class="table-container">
    @if($catalogs->count() > 0)
    <table class="catalog-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Batch</th>
                <th>Expiry</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($catalogs as $c)
            @php
                $isExpiringSoon = \Carbon\Carbon::parse($c->expiry_date)->diffInDays(now()) < 90;
                $isLowStock = $c->real_stock < 10;
            @endphp
            <tr>
                <td>
                    <strong style="color: #0f172a;">{{ $c->item->name ?? 'N/A' }}</strong>
                    @if($c->item && $c->item->generic_name)
                    <div style="font-size: 0.7rem; color: #64748b; margin-top: 4px;">
                        {{ $c->item->generic_name }}
                    </div>
                    @endif
                </td>
                <td>
                    <span class="batch-info">
                        <i class="fa fa-barcode me-1" style="font-size: 0.7rem;"></i>
                        {{ $c->batch_no }}
                    </span>
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($c->expiry_date)->format('d M, Y') }}
                    @if($isExpiringSoon && $c->expiry_date > now())
                        <span style="display: block; font-size: 0.65rem; color: #eab308; margin-top: 4px;">
                            <i class="fa fa-exclamation-triangle"></i> Expiring soon
                        </span>
                    @elseif($c->expiry_date <= now())
                        <span style="display: block; font-size: 0.65rem; color: #ef4444; margin-top: 4px;">
                            <i class="fa fa-times-circle"></i> Expired
                        </span>
                    @endif
                </td>
                <td>
                    <span class="price-tag">
                        ₹{{ number_format($c->retailer_price, 2) }}
                        <small>MRP: ₹{{ number_format($c->mrp ?? $c->retailer_price, 2) }}</small>
                    </span>
                </td>
                <td>
                    <span class="badge-stock {{ $isLowStock && $c->current_stock > 0 ? 'low' : ($c->current_stock == 0 ? 'low' : '') }}">
                        <i class="fa {{ $c->current_stock > 10 ? 'fa-boxes' : 'fa-exclamation-triangle' }}"></i>
                        {{ $c->current_stock }} units
                    </span>
                    @if($c->current_stock == 0)
                        <span style="display: block; font-size: 0.65rem; color: #ef4444; margin-top: 4px;">Out of stock</span>
                    @elseif($isLowStock && $c->current_stock > 0)
                        <span style="display: block; font-size: 0.65rem; color: #eab308; margin-top: 4px;">Low stock</span>
                    @endif
                </td>
             <td>
    <div class="action-buttons">

        {{-- VIEW --}}
        <a href="{{ route('supplier.catalogs.show', $c->id) }}" class="btn-action btn-view">
            <i class="fa fa-eye"></i> View
        </a>

        {{-- EDIT --}}
        <a href="{{ route('supplier.catalogs.edit', $c->id) }}" class="btn-action btn-edit">
            <i class="fa fa-edit"></i> Edit
        </a>

        {{-- DELETE --}}
        <form action="{{ route('supplier.catalogs.destroy', $c->id) }}" 
              method="POST"
              onsubmit="return confirm('Are you sure you want to delete this catalog?')">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn-action btn-delete">
                <i class="fa fa-trash"></i> Delete
            </button>
        </form>

    </div>
</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state">
        <i class="fa fa-book-open"></i>
        <h5>No Catalogs Found</h5>
        <p>Get started by adding your first product catalog</p>
        <a href="{{ route('supplier.catalogs.create') }}" class="btn btn-add-catalog" style="display: inline-flex; margin-top: 16px;">
            <i class="fa fa-plus me-2"></i> Add Catalog
        </a>
    </div>
    @endif
</div>

@endsection