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

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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

    /* Filter Bar */
    .filter-bar {
        background: white;
        border-radius: 20px;
        padding: 16px 24px;
        margin-bottom: 24px;
        border: 1px solid #eef2ff;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 160px;
    }

    .filter-group label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #64748b;
        display: block;
        margin-bottom: 6px;
    }

    .filter-group select,
    .filter-group input {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.85rem;
        background: #fafcff;
        transition: all 0.2s;
    }

    .filter-group select:focus,
    .filter-group input:focus {
        outline: none;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 12px;
    }

    .btn-filter {
        background: #0ea5e9;
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.8rem;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-filter:hover {
        background: #0284c7;
        transform: translateY(-1px);
    }

    .btn-reset {
        background: #f1f5f9;
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.8rem;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-reset:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* Table Styles */
    .table-container {
        background: white;
        border-radius: 20px;
        border: 1px solid #eef2ff;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .items-table thead tr {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .items-table th {
        padding: 16px 16px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #475569;
        text-align: left;
        white-space: nowrap;
    }

    .items-table td {
        padding: 16px 16px;
        font-size: 0.85rem;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .items-table tbody tr:hover {
        background: #f8fafc;
        transition: background 0.2s ease;
    }

    /* Product Image */
    .product-image {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }

    .product-image-placeholder {
        width: 48px;
        height: 48px;
        background: #f1f5f9;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 0.7rem;
    }

    /* Item Name */
    .item-name {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .item-sku {
        font-size: 0.65rem;
        color: #94a3b8;
        font-family: monospace;
    }

    /* Badges */
    .badge-custom {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .badge-gst {
        background: #e0f2fe;
        color: #0284c7;
    }

    .badge-hsn {
        background: #f1f5f9;
        color: #475569;
        font-family: monospace;
    }

    /* Action Button */
    .btn-view {
        background: #eff6ff;
        border: none;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #2563eb;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-view:hover {
        background: #dbeafe;
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

    /* Pagination */
    .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: flex-end;
    }

    .pagination-wrapper nav {
        display: inline-block;
    }

    .pagination-wrapper .pagination {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin: 0;
        padding: 0;
    }

    .pagination-wrapper .page-item {
        list-style: none;
    }

    .pagination-wrapper .page-link {
        padding: 8px 14px;
        border-radius: 12px;
        font-size: 0.8rem;
        text-decoration: none;
        background: #f1f5f9;
        color: #475569;
        border: none;
        transition: all 0.2s;
    }

    .pagination-wrapper .page-link:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .pagination-wrapper .active .page-link {
        background: linear-gradient(135deg, #0ea5e9, #3b82f6);
        color: white;
    }

    .pagination-wrapper .disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .items-table {
            min-width: 1000px;
        }
        
        .table-container {
            overflow-x: auto;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .filter-bar {
            flex-direction: column;
        }

        .filter-group {
            width: 100%;
        }

        .filter-actions {
            width: 100%;
        }

        .filter-actions button {
            flex: 1;
            justify-content: center;
        }

        .pagination-wrapper {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
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

    .table-container {
        animation: fadeIn 0.3s ease;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <h4>
                <i class="fa fa-capsules"></i>
                My Items
            </h4>
            <p>View all your registered pharmaceutical products</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa fa-cubes"></i>
            </div>
            <div class="stat-info">
                <h4>{{ $items->total() }}</h4>
                <p>Total Items</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa fa-tag"></i>
            </div>
            <div class="stat-info">
                <h4>{{ $items->where('brand', '!=', null)->count() }}</h4>
                <p>With Brand</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa fa-building"></i>
            </div>
            <div class="stat-info">
                <h4>{{ $items->where('manufacturer_id', '!=', null)->count() }}</h4>
                <p>Manufacturers</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa fa-image"></i>
            </div>
            <div class="stat-info">
                <h4>{{ $items->where('main_image_url', '!=', null)->count() }}</h4>
                <p>With Images</p>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="filter-group">
            <label><i class="fa fa-search"></i> Search Item</label>
            <input type="text" id="searchInput" placeholder="Search by name, brand, or manufacturer...">
        </div>
        <div class="filter-group">
            <label><i class="fa fa-filter"></i> Category</label>
            <select id="categoryFilter">
                <option value="">All Categories</option>
                @php
                    $categories = $items->pluck('category.name')->unique()->filter();
                @endphp
                @foreach($categories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-actions">
            <button class="btn-filter" onclick="applyFilters()">
                <i class="fa fa-search"></i> Apply
            </button>
            <button class="btn-reset" onclick="resetFilters()">
                <i class="fa fa-undo"></i> Reset
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        @if($items->count() > 0)
        <table class="items-table" id="itemsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Item Name</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Sub Category</th>
                    <th>GST%</th>
                    <th>HSN</th>
                    <th>Manufacturer</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse($items as $key => $item)
                <tr data-name="{{ strtolower($item->name) }}" 
                    data-brand="{{ strtolower($item->brand ?? '') }}"
                    data-manufacturer="{{ strtolower($item->manufacturer->name ?? '') }}"
                    data-category="{{ $item->category->name ?? '' }}">
                    <td>{{ $items->firstItem() + $key }}</td>
                    <td>
                        @if($item->main_image_url)
                            <img src="{{ $item->main_image_url }}" 
                                 class="product-image"
                                 alt="{{ $item->name }}"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="product-image-placeholder" style="display: none;">
                                <i class="fa fa-image"></i>
                            </div>
                        @else
                            <div class="product-image-placeholder">
                                <i class="fa fa-image"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="item-name">{{ $item->name }}</div>
                        @if($item->generic_name)
                            <div class="item-sku">{{ $item->generic_name }}</div>
                        @endif
                    </td>
                    <td>{{ $item->brand ?? '-' }}</td>
                    <td>
                        <span class="badge-custom" style="background:#e0f2fe; color:#0369a1;">
                            {{ $item->category->name ?? '-' }}
                        </span>
                    </td>
                    <td>{{ $item->subCategory->name ?? '-' }}</td>
                    <td>
                        <span class="badge-custom badge-gst">{{ $item->gst_percent ?? 0 }}%</span>
                    </td>
                    <td>
                        <span class="badge-custom badge-hsn">{{ $item->hsn_code ?? '-' }}</span>
                    </td>
                    <td>
                        @if($item->manufacturer)
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <i class="fa fa-building" style="color: #94a3b8; font-size: 0.7rem;"></i>
                                <span>{{ $item->manufacturer->name }}</span>
                            </div>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('supplier.items.show', $item->id) }}" class="btn-view">
                            <i class="fa fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="empty-state">
                        <i class="fa fa-box-open"></i>
                        <h5>No Items Found</h5>
                        <p>You haven't added any items to your catalog yet.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <i class="fa fa-capsules"></i>
            <h5>No Items Found</h5>
            <p>You haven't added any items to your catalog yet.</p>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($items->hasPages())
    <div class="pagination-wrapper">
        {{ $items->links() }}
    </div>
    @endif
</div>

<script>
    // Filter functions
    function applyFilters() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const categoryFilter = document.getElementById('categoryFilter').value;
        
        const rows = document.querySelectorAll('#tableBody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            let show = true;
            
            // Search filter
            if (searchTerm) {
                const name = row.getAttribute('data-name') || '';
                const brand = row.getAttribute('data-brand') || '';
                const manufacturer = row.getAttribute('data-manufacturer') || '';
                
                if (!name.includes(searchTerm) && !brand.includes(searchTerm) && !manufacturer.includes(searchTerm)) {
                    show = false;
                }
            }
            
            // Category filter
            if (show && categoryFilter) {
                const category = row.getAttribute('data-category') || '';
                if (category !== categoryFilter) show = false;
            }
            
            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });
        
        // Update showing message if needed
        const showingInfo = document.querySelector('.showing-info');
        if (showingInfo) {
            showingInfo.innerHTML = `<i class="fa fa-database"></i> Showing ${visibleCount} of ${rows.length} items`;
        }
    }
    
    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('categoryFilter').value = '';
        
        const rows = document.querySelectorAll('#tableBody tr');
        rows.forEach(row => {
            row.style.display = '';
        });
    }
    
    // Real-time search with Enter key
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') applyFilters();
    });
    
    // Image error handling
    document.querySelectorAll('.product-image').forEach(img => {
        img.addEventListener('error', function() {
            this.style.display = 'none';
            if (this.nextElementSibling) {
                this.nextElementSibling.style.display = 'flex';
            }
        });
    });
</script>

@endsection