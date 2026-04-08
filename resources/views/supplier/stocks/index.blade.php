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

    .stat-icon.inbound {
        background: linear-gradient(135deg, #dcfce7, #d1fae5);
    }

    .stat-icon.inbound i {
        color: #10b981;
    }

    .stat-icon.outbound {
        background: linear-gradient(135deg, #fee2e2, #fef2f2);
    }

    .stat-icon.outbound i {
        color: #ef4444;
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
        min-width: 180px;
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

    .stock-table {
        width: 100%;
        border-collapse: collapse;
    }

    .stock-table thead tr {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .stock-table th {
        padding: 16px 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #475569;
        text-align: left;
    }

    .stock-table td {
        padding: 16px 20px;
        font-size: 0.85rem;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .stock-table tbody tr:hover {
        background: #f8fafc;
        transition: background 0.2s ease;
    }

    /* Type Badges */
    .badge-type {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-inbound {
        background: #dcfce7;
        color: #15803d;
    }

    .badge-outbound {
        background: #fee2e2;
        color: #b91c1c;
    }

    .badge-adjustment {
        background: #fef3c7;
        color: #b45309;
    }

    /* Quantity Styles */
    .qty-positive {
        color: #10b981;
        font-weight: 700;
    }

    .qty-negative {
        color: #ef4444;
        font-weight: 700;
    }

    .qty-neutral {
        color: #64748b;
    }

    /* Date Cell */
    .date-cell {
        font-size: 0.75rem;
        color: #64748b;
    }

    .date-cell i {
        margin-right: 4px;
        font-size: 0.7rem;
    }

    /* Action Buttons */
    .delete-form {
        display: inline-block;
    }

    .btn-delete {
        background: #fef2f2;
        border: none;
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 600;
        color: #dc2626;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
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

    /* Pagination */
    .pagination-container {
        padding: 20px 24px;
        background: white;
        border-top: 1px solid #eef2ff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .pagination {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .pagination a, .pagination span {
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 0.8rem;
        text-decoration: none;
        transition: all 0.2s;
        background: #f1f5f9;
        color: #475569;
    }

    .pagination a:hover {
        background: #0ea5e9;
        color: white;
    }

    .pagination .active {
        background: #0ea5e9;
        color: white;
    }

    .showing-info {
        font-size: 0.75rem;
        color: #64748b;
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

        .filter-bar {
            flex-direction: column;
        }

        .filter-group {
            width: 100%;
        }

        .filter-actions {
            display: flex;
            gap: 12px;
            width: 100%;
        }

        .filter-actions button {
            flex: 1;
            justify-content: center;
        }

        .stock-table th,
        .stock-table td {
            padding: 12px 16px;
        }

        /* Make table horizontally scrollable on mobile */
        .table-container {
            overflow-x: auto;
        }

        .stock-table {
            min-width: 600px;
        }

        .pagination-container {
            flex-direction: column;
            align-items: center;
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

<div class="page-header">
    <div class="page-title">
        <h3>
            <i class="fa fa-history"></i>
            Stock History
        </h3>
        <p>Track all inventory movements - purchases, sales, and adjustments</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa fa-chart-line"></i>
        </div>
        <div class="stat-info">
            <h4>{{ $stocks->count() }}</h4>
            <p>Total Transactions</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon inbound">
            <i class="fa fa-arrow-down"></i>
        </div>
        <div class="stat-info">
            <h4>{{ $stocks->where('type', 'inbound')->sum('qty') }}</h4>
            <p>Stock In</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon outbound">
            <i class="fa fa-arrow-up"></i>
        </div>
        <div class="stat-info">
            <h4>{{ $stocks->where('type', 'outbound')->sum('qty') }}</h4>
            <p>Stock Out</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa fa-cubes"></i>
        </div>
        <div class="stat-info">
            <h4>{{ $stocks->where('type', 'inbound')->sum('qty') - $stocks->where('type', 'outbound')->sum('qty') }}</h4>
            <p>Net Balance</p>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <div class="filter-group">
        <label><i class="fa fa-filter"></i> Stock Type</label>
        <select id="typeFilter">
            <option value="all">All Transactions</option>
            <option value="inbound">Stock In (Purchase)</option>
            <option value="outbound">Stock Out (Sale)</option>
            <option value="adjustment">Adjustment</option>
        </select>
    </div>
    <div class="filter-group">
        <label><i class="fa fa-calendar"></i> Date From</label>
        <input type="date" id="dateFrom">
    </div>
    <div class="filter-group">
        <label><i class="fa fa-calendar"></i> Date To</label>
        <input type="date" id="dateTo">
    </div>
    <div class="filter-group">
        <label><i class="fa fa-search"></i> Search Item/Batch</label>
        <input type="text" id="searchInput" placeholder="Search by item name or batch...">
    </div>
    <div class="filter-actions">
        <button class="btn-filter" onclick="applyFilters()">
            <i class="fa fa-search"></i> Apply Filters
        </button>
        <button class="btn-reset" onclick="resetFilters()">
            <i class="fa fa-undo"></i> Reset
        </button>
    </div>
</div>

<!-- Table -->
<div class="table-container">
    @if($stocks->count() > 0)
    <table class="stock-table" id="stockTable">
        <thead>
            <tr>
                <th>Item</th>
                <th>Batch</th>
                <th>Quantity</th>
                <th>Type</th>
                <th>Date & Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="stockTableBody">
            @foreach($stocks as $s)
            <tr data-type="{{ $s->type }}" data-date="{{ $s->created_at }}" data-search="{{ strtolower($s->catalog->item->name ?? '') }} {{ strtolower($s->catalog->batch_no ?? '') }}">
                <td>
                    <strong style="color: #0f172a;">{{ $s->catalog->item->name ?? 'N/A' }}</strong>
                    @if($s->catalog->item && $s->catalog->item->generic_name)
                        <div style="font-size: 0.7rem; color: #64748b; margin-top: 4px;">
                            {{ $s->catalog->item->generic_name }}
                        </div>
                    @endif
                </td>
                <td>
                    <span style="font-family: monospace; font-size: 0.8rem; background: #f1f5f9; padding: 4px 10px; border-radius: 20px;">
                        <i class="fa fa-barcode" style="font-size: 0.7rem;"></i> {{ $s->catalog->batch_no }}
                    </span>
                </td>
                <td>
                    <span class="qty-{{ $s->type == 'inbound' ? 'positive' : ($s->type == 'outbound' ? 'negative' : 'neutral') }}">
                        {{ $s->type == 'inbound' ? '+' : ($s->type == 'outbound' ? '-' : '') }}{{ $s->qty }}
                    </span>
                    <div style="font-size: 0.65rem; color: #94a3b8; margin-top: 4px;">
                        @if($s->reference_type)
                            Ref: {{ $s->reference_type }} #{{ $s->reference_id }}
                        @endif
                    </div>
                 </td>
                <td>
                    @if($s->type == 'inbound')
                        <span class="badge-type badge-inbound">
                            <i class="fa fa-arrow-down"></i> Stock In
                        </span>
                    @elseif($s->type == 'outbound')
                        <span class="badge-type badge-outbound">
                            <i class="fa fa-arrow-up"></i> Stock Out
                        </span>
                    @else
                        <span class="badge-type badge-adjustment">
                            <i class="fa fa-edit"></i> Adjustment
                        </span>
                    @endif
                 </td>
                <td>
                    <div class="date-cell">
                        <i class="fa fa-calendar-alt"></i> {{ $s->created_at->format('d M, Y') }}
                    </div>
                    <div style="font-size: 0.65rem; color: #94a3b8; margin-top: 4px;">
                        <i class="fa fa-clock"></i> {{ $s->created_at->format('h:i A') }}
                    </div>
                 </td>
                <td>
                    <form method="POST" action="{{ route('supplier.stocks.destroy', $s->id) }}" 
                          class="delete-form"
                          onsubmit="return confirm('Are you sure you want to delete this stock entry? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </form>
                 </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Pagination -->
    <div class="pagination-container">
        <div class="showing-info">
            <i class="fa fa-database"></i> Showing <span id="showingCount">{{ $stocks->count() }}</span> of {{ $stocks->count() }} entries
        </div>
        <div class="pagination" id="pagination">
            {{ $stocks->links() }}
        </div>
    </div>
    @else
    <div class="empty-state">
        <i class="fa fa-box-open"></i>
        <h5>No Stock History Found</h5>
        <p>No stock movements have been recorded yet.</p>
    </div>
    @endif
</div>

<script>
    // Filter functions
    function applyFilters() {
        const typeFilter = document.getElementById('typeFilter').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        
        const rows = document.querySelectorAll('#stockTableBody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            let show = true;
            
            // Type filter
            if (typeFilter !== 'all') {
                const rowType = row.getAttribute('data-type');
                if (rowType !== typeFilter) show = false;
            }
            
            // Date filter
            if (show && (dateFrom || dateTo)) {
                const rowDate = row.getAttribute('data-date').split(' ')[0];
                if (dateFrom && rowDate < dateFrom) show = false;
                if (dateTo && rowDate > dateTo) show = false;
            }
            
            // Search filter
            if (show && searchTerm) {
                const searchText = row.getAttribute('data-search') || '';
                if (!searchText.includes(searchTerm)) show = false;
            }
            
            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });
        
        document.getElementById('showingCount').innerText = visibleCount;
    }
    
    function resetFilters() {
        document.getElementById('typeFilter').value = 'all';
        document.getElementById('dateFrom').value = '';
        document.getElementById('dateTo').value = '';
        document.getElementById('searchInput').value = '';
        
        const rows = document.querySelectorAll('#stockTableBody tr');
        rows.forEach(row => {
            row.style.display = '';
        });
        
        document.getElementById('showingCount').innerText = rows.length;
    }
    
    // Add event listeners for real-time search
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') applyFilters();
    });
    
    // Export functionality (optional)
    function exportToCSV() {
        const rows = document.querySelectorAll('#stockTableBody tr:visible');
        let csv = "Item,Batch,Quantity,Type,Date\n";
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 5) {
                const item = cells[0].innerText.replace(/,/g, ' ').replace(/\n/g, ' ');
                const batch = cells[1].innerText.replace(/,/g, ' ').replace(/\n/g, ' ');
                const qty = cells[2].innerText.replace(/,/g, ' ').replace(/\n/g, ' ');
                const type = cells[3].innerText.replace(/,/g, ' ').replace(/\n/g, ' ');
                const date = cells[4].innerText.replace(/,/g, ' ').replace(/\n/g, ' ');
                csv += `"${item}","${batch}","${qty}","${type}","${date}"\n`;
            }
        });
        
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'stock_history.csv';
        a.click();
        URL.revokeObjectURL(url);
    }
</script>

@endsection