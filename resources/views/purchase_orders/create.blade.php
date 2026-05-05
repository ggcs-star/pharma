@extends('layouts.master')

@section('title', 'Create Purchase Order')

@section('content')

<div class="container-fluid px-4 py-3">
    <div class="dashboard-card">
        <div class="card-header-custom">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="pharma-badge mb-2 d-inline-block">
                        <i class="bi bi-capsule me-1"></i> B2B Pharma Distributor
                    </span>
                    <h2 class="fw-bold mb-1" style="color: #0f172a;">
                        <i class="bi bi-cart-plus me-2" style="color: #198754;"></i>Create Purchase Order
                    </h2>
                    <p class="text-secondary small mb-0">Search medicine → Select supplier → Add multiple items</p>
                </div>
            </div>
        </div>
        <div class="p-4 p-lg-5 pt-0">
            <form method="POST" action="{{ route('purchase-orders.store') }}" id="purchaseForm">
                @csrf
                <div class="mb-4 order-date-box">
                    <label class="form-label fw-semibold small text-secondary">Order Date <span class="text-danger">*</span></label>
                    <input type="date" name="order_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                
                <!-- STEP 1: Search Medicine -->
                <div class="medicine-search-section">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-search text-success fs-5"></i>
                        <h5 class="fw-bold mb-0">1. Search Medicine</h5>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary ms-2">Type to search and select a medicine</span>
                    </div>
                    <select id="medicineSearch" class="form-control" placeholder="🔍 Search for a medicine...">
                        <option value="">Select Medicine</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-image="{{ $item->main_image_url ?? '' }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- STEP 2: Suppliers for selected medicine (hidden initially) -->
                <div id="suppliersSection" style="display: none;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-building-check text-success fs-5"></i>
                        <h5 class="fw-bold mb-0">2. Select Supplier for this Medicine</h5>
                    </div>
                    <div id="supplierCardsContainer" class="supplier-cards-grid">
                        <div class="text-center p-4 w-100"><div class="loader-spinner"></div><p class="mt-2">Loading suppliers...</p></div>
                    </div>
                </div>
                
                <!-- Selected Supplier Info (hidden initially) -->
                <div id="selectedSupplierInfo" style="display: none;" class="mb-3 mt-3">
                    <div class="selected-supplier-info">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>Selected Supplier: <strong id="selectedSupplierName">-</strong></span>
                        <span id="selectedSupplierMedicine" class="badge-supplier ms-2"></span>
                        <button type="button" id="changeSupplierBtn" class="btn btn-sm btn-outline-secondary rounded-pill">Change Supplier</button>
                    </div>
                </div>
                
                <!-- STEP 3: All Items from selected supplier (multiple items can be added) -->
                <div id="itemsSection" style="display: none;">
                    <div class="supplier-items-section">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                            <div>
                                <i class="bi bi-box-seam text-success fs-5"></i>
                                <h5 class="fw-bold d-inline-block ms-2 mb-0">3. Add Items from this Supplier</h5>
                                <span class="badge bg-info bg-opacity-10 text-info ms-2">Click any item below to add</span>
                            </div>
                        </div>
                        
                        <!-- Order Items Table -->
                        <div class="table-responsive mb-4">
                            <table class="table items-table">
                                <thead>
                                    <tr>
                                        <th style="width: 35%;">Medicine Item</th>
                                        <th style="width: 10%;">Qty</th>
                                        <th style="width: 12%;">PTR (₹)</th>
                                        <th style="width: 12%;">MRP (₹)</th>
                                        <th style="width: 10%;">GST%</th>
                                        <th style="width: 12%;">Total (₹)</th>
                                        <th style="width: 6%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="orderItemsBody">
                                    <tr><td colspan="7" class="text-center text-muted py-4">No items added yet. Click on products below to add.</td></tr>
                                </tbody>
                            </table>
                            <div class="mt-4 text-end">
    <div class="p-3 rounded-4 border bg-light d-inline-block"
         style="min-width:320px;">

        <div class="d-flex justify-content-between mb-2">
            <strong>Sub Total:</strong>
            <span id="subTotalValue">₹0.00</span>
        </div>

        <div class="d-flex justify-content-between mb-2">
            <strong>Total GST Amount:</strong>
            <span id="gstTotalValue">₹0.00</span>
        </div>

        <hr>

        <div class="d-flex justify-content-between">
            <strong class="fs-5 text-success">
                Grand Total:
            </strong>
            <strong class="fs-5 text-success"
                    id="grandTotalValue">
                ₹0.00
            </strong>
        </div>
    </div>
</div>
                        </div>
                        
                        <!-- All Items from this supplier (catalog) -->
                        <div class="mt-3">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-grid-3x3-gap-fill text-secondary"></i>
                                <span class="fw-semibold">All Items Available from this Supplier (Click to add to order)</span>
                            </div>
                            <div id="supplierCatalogGrid" class="row g-3" style="max-height: 400px; overflow-y: auto;">
                                <!-- catalog items will appear here -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">
                    <div></div>
                    <button type="submit" class="btn btn-save">
                        <i class="bi bi-check2-circle me-1"></i> Save Purchase Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<style>
    * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    
    .dashboard-card {
        background: #ffffff;
        border-radius: 36px;
        box-shadow: 0 20px 40px -14px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    .card-header-custom {
        background: linear-gradient(135deg, #ffffff 0%, #fbfdfe 100%);
        border-bottom: 1px solid #eef2f8;
        padding: 1.75rem 2rem;
    }
    .pharma-badge {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6d9 100%);
        color: #1b5e2a;
        padding: 6px 14px;
        border-radius: 40px;
        font-size: 0.7rem;
        font-weight: 700;
    }
    
    .items-table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0 12px;
    }
    .custom-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #198754;
    color: white;
    padding: 12px 18px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    transform: translateX(120%);
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 9999;
}

.custom-toast.show {
    transform: translateX(0);
    opacity: 1;
}
    .items-table thead th {
        background: #f8fafd;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #2c3e66;
        border-bottom: 2px solid #e9edf2;
        padding: 16px 12px;
    }
    .items-table tbody td {
        vertical-align: middle;
        padding: 12px 10px;
        background: white;
        border: none;
    }
    .item-main-row {
        background: white;
        border-radius: 24px;
        transition: all 0.2s;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    
    .form-control, .ts-control {
        border-radius: 20px;
        border: 1.5px solid #e2e8f0;
        padding: 10px 14px;
        font-size: 0.85rem;
    }
    .form-control:focus, .ts-wrapper.focus .ts-control {
        border-color: #198754;
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.12);
    }
    .ts-wrapper { min-width: 260px; }
    
    .medicine-search-section {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border-radius: 28px;
        border: 2px solid #e9edf2;
        padding: 24px;
        margin-bottom: 24px;
    }
    
    .supplier-cards-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 8px 0;
        max-height: 450px;
        overflow-y: auto;
    }
    .supplier-select-card {
        background: white;
        border-radius: 24px;
        border: 2px solid #edf2f9;
        padding: 18px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 320px;
        position: relative;
    }
    .supplier-select-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 28px -12px rgba(0, 0, 0, 0.12);
        border-color: #cbd5e1;
    }
    .supplier-select-card.selected-supplier {
        border: 2px solid #198754;
        background: linear-gradient(135deg, #f0fff4 0%, #fafef8 100%);
    }
    .supplier-select-card.selected-supplier::after {
        content: "✓ Selected";
        position: absolute;
        top: 12px;
        right: 12px;
        background: #198754;
        color: white;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 30px;
    }
    .supplier-select-card.best-price {
        border: 2px solid #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #fffef7 100%);
    }
    .supplier-select-card.best-price::before {
        content: "🏆 BEST PRICE";
        position: absolute;
        top: -10px;
        left: 16px;
        background: #f59e0b;
        color: white;
        font-size: 0.65rem;
        font-weight: 800;
        padding: 3px 12px;
        border-radius: 40px;
        z-index: 2;
    }
    .supplier-img {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: 20px;
        background: #f8fafc;
        border: 1px solid #eef2f0;
    }
    .price-chip {
        background: #eef2ff;
        color: #1e3a8a;
        border-radius: 40px;
        padding: 4px 10px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .supplier-items-section {
        background: #ffffff;
        border-radius: 28px;
        border: 1px solid #eef2f8;
        padding: 20px;
        margin-top: 20px;
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .catalog-item-card {
        cursor: pointer;
        transition: all 0.25s ease;
        border-radius: 20px;
        border: 1px solid #eef2f8;
        background: white;
        padding: 14px;
        text-align: center;
    }
    .catalog-item-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        border-color: #198754;
    }
    
    .btn-save {
        background: linear-gradient(135deg, #198754 0%, #0f5c3a 100%);
        border: none;
        border-radius: 60px;
        padding: 12px 42px;
        font-weight: 700;
        box-shadow: 0 6px 14px rgba(25, 135, 84, 0.25);
        color: white;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(25, 135, 84, 0.35);
        color: white;
    }
   .remove-row-btn {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    border: none;
    background: #fff1f2;
    color: #dc2626;
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;

    display: flex;
    align-items: center;
    justify-content: center;

    transition: all 0.2s ease;
}

.remove-row-btn:hover {
    background: #dc2626;
    color: #ffffff;
    transform: scale(1.08);
    box-shadow: 0 8px 20px rgba(220, 38, 38, 0.18);
}
    .loader-spinner {
        display: inline-block;
        width: 1.5rem;
        height: 1.5rem;
        border: 2px solid #e2e8f0;
        border-top-color: #198754;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .total-field {
        background: #f4f9f6;
        font-weight: 700;
        color: #198754;
    }
    .order-date-box { max-width: 280px; }
    .selected-supplier-info {
        background: #e8f5e9;
        border-radius: 16px;
        padding: 12px 20px;
        display: inline-flex;
        align-items: center;
        gap: 12px;
    }
    .badge-supplier {
        background: #198754;
        color: white;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 0.75rem;
    }
    @media (max-width: 768px) {
        .supplier-select-card { width: calc(100% - 10px); }
        .card-header-custom { padding: 1.2rem; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>


    function formatExpiry(dateStr) {
    if (!dateStr) return 'N/A';

    const date = new Date(dateStr);
    if (isNaN(date)) return 'N/A';

    const today = new Date();
    const diffMonths = (date.getFullYear() - today.getFullYear()) * 12 
                     + (date.getMonth() - today.getMonth());

    const formatted = date.toLocaleDateString('en-IN', {
        month: '2-digit',
        year: 'numeric'
    });

    if (diffMonths < 0) return `❌ ${formatted}`;
    if (diffMonths <= 3) return `⚠️ ${formatted}`;

    return formatted;
}
(function() {
    'use strict';
    
    let selectedMedicineId = null;
    let selectedMedicineName = null;
    let selectedSupplierData = null;
    let supplierCatalogItems = [];
    let orderItems = [];
    let rowIdCounter = 0;
       
function updateFinalTotals() {
    let subtotal = 0;
    let gstTotal = 0;
    let grandTotal = 0;

    orderItems.forEach(item => {
        subtotal += parseFloat(item.subtotal || 0);
        gstTotal += parseFloat(item.gst_amount || 0);
        grandTotal += parseFloat(item.total || 0);
    });

    document.getElementById('subTotalValue').innerText =
        `₹${subtotal.toFixed(2)}`;

    document.getElementById('gstTotalValue').innerText =
        `₹${gstTotal.toFixed(2)}`;

    document.getElementById('grandTotalValue').innerText =
        `₹${grandTotal.toFixed(2)}`;
}
    
    function getImageUrl(data) {
        const item = data?.item || data;
        return item?.image_url || item?.main_image_url || 
            (item?.main_image ? (item.main_image.startsWith('http') ? item.main_image : 'https://pharma-catalog-assets.s3.us-east-1.amazonaws.com/' + item.main_image) : null) ||
            'https://placehold.co/600x400?text=Medicine';
    }
    
    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }
    
    function findBestPriceSupplier(suppliers) {
        if (!suppliers || suppliers.length === 0) return null;
        return suppliers.reduce((best, current) => {
         const currPrice = current.retailer_price || current.purchase_price || Infinity;
const bestPrice = best.retailer_price || best.purchase_price || Infinity;
            return currPrice < bestPrice ? current : best;
        }, suppliers[0]);
    }
    
    function renderSupplierCards(suppliers) {
        if (!suppliers || suppliers.length === 0) {
            return '<div class="text-center text-muted w-100 p-4"><i class="bi bi-building-slash fs-1"></i><p class="mt-2">No suppliers available for this medicine</p></div>';
        }
        const bestSupplier = findBestPriceSupplier(suppliers);
        const bestId = bestSupplier ? bestSupplier.id : null;
        let html = '';
suppliers.forEach((sup, index) => {            const isBest = bestId === sup.id;
            const imgUrl = getImageUrl(sup);
            html += `
                <div class="supplier-select-card ${isBest ? 'best-price' : ''}" 
  data-index="${index}">                  <div class="d-flex gap-3">
                        <img src="${imgUrl}" class="supplier-img" alt="medicine" onerror="this.src='https://placehold.co/600x400?text=Medicine'">
                        <div class="flex-grow-1">
                            <div class="fw-bold mb-1">${escapeHtml(sup.item?.name || selectedMedicineName || 'Medicine')}</div>
                            <div class="small fw-semibold text-secondary mb-2">
                                <i class="bi bi-building"></i> ${escapeHtml(sup.supplier?.name || 'Supplier')}
                            </div>
                            <div class="d-flex flex-wrap gap-2">
<span class="price-chip">
    💰 PTR: ₹${sup.retailer_price || sup.purchase_price || 0}
</span>                                <span class="price-chip">🏷️ MRP: ₹${sup.base_price || sup.mrp || 0}</span>
<span class="price-chip">⏳ Exp: ${formatExpiry(sup.expiry_date)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        return html;
    }
    
    async function loadSuppliersForMedicine(medicineId, medicineName) {
        const suppliersSection = document.getElementById('suppliersSection');
        const container = document.getElementById('supplierCardsContainer');
        if (!container) return;
        
        suppliersSection.style.display = 'block';
        container.innerHTML = `<div class="text-center p-4 w-100"><div class="loader-spinner"></div><p class="mt-2">Loading suppliers for ${escapeHtml(medicineName)}...</p></div>`;
        
        try {
            const response = await fetch(`/item/suppliers?item_id=${medicineId}`);
            if (!response.ok) throw new Error('Network error');
            const suppliers = await response.json();
            
            if (!suppliers || suppliers.length === 0) {
                container.innerHTML = '<div class="text-center text-muted p-4">⚠️ No suppliers found for this medicine</div>';
                return;
            }
            
            container.innerHTML = renderSupplierCards(suppliers);
            
            document.querySelectorAll('.supplier-select-card').forEach(card => {
                card.addEventListener('click', async (e) => {
const index = card.dataset.index;
const supplier = suppliers[index];
await selectSupplier(supplier, card);                            });
            });
        } catch (err) {
            console.error(err);
            container.innerHTML = '<div class="text-center text-danger p-4">⚠️ Failed to load suppliers</div>';
        }
    }
    
    async function selectSupplier(supplier, selectedCard) {
        document.querySelectorAll('.supplier-select-card').forEach(c => c.classList.remove('selected-supplier'));
        selectedCard.classList.add('selected-supplier');
        
        selectedSupplierData = supplier;
        const supplierId = supplier.supplier_id ?? supplier.id;
        const supplierName = supplier.supplier?.name || 'Supplier';
        
        document.getElementById('selectedSupplierName').innerText = supplierName;
        document.getElementById('selectedSupplierMedicine').innerText = selectedMedicineName;
        document.getElementById('selectedSupplierInfo').style.display = 'block';
        
        try {
            const catRes = await fetch(`/api/supplier-items/${supplierId}`);
            if (!catRes.ok) throw new Error('Catalog fetch failed');
            const catalog = await catRes.json();
            supplierCatalogItems = catalog || [];
            
            renderCatalogGrid();
            document.getElementById('itemsSection').style.display = 'block';
            orderItems = [];
            renderOrderItemsTable();
            
           showRightToast(`Supplier "${supplierName}" selected`);
        } catch (err) {
            console.error('Catalog error:', err);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load supplier items' });
        }
    }
    function showRightToast(message) {
    let toast = document.createElement('div');
    toast.className = 'custom-toast';
    toast.innerText = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('show');
    }, 50);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}
    function renderCatalogGrid() {
        const grid = document.getElementById('supplierCatalogGrid');
        if (!grid) return;
        if (!supplierCatalogItems.length) {
            grid.innerHTML = '<div class="col-12 text-muted text-center py-4">📦 No items available from this supplier</div>';
            return;
        }
        let html = '';
supplierCatalogItems.forEach((item, index) => {

    const stock = item.current_stock ?? item.real_stock ?? 0;

    const isExpired = item.expiry_date
        ? new Date(item.expiry_date) < new Date(new Date().setHours(0,0,0,0))
        : false;

    // expired + out of stock item bilkul mat dikhao
    if (stock <= 0 || isExpired) {
        return;
    }      const imgUrl = getImageUrl(item);
            html += `
                <div class="col-md-3 col-sm-6">
                    <div class="catalog-item-card" data-index="${index}">                        <img src="${imgUrl}" class="img-fluid rounded-3 mb-2" style="height:85px;width:100%;object-fit:cover;" onerror="this.src='https://placehold.co/600x400?text=Medicine'">
                        <div class="small fw-semibold">${escapeHtml(item.item?.name || 'Product')}</div>
                        <div class="small text-primary fw-semibold mt-1">
    📦 1 ${item.item?.pack_type || 'Pack'} of 
    ${item.item?.number_of_units || 1} 
    ${item.item?.unit || 'Unit'}
</div>
<div class="text-success fw-bold mt-1">
    ₹${item.retailer_price || item.purchase_price || 0}
</div><div class="d-flex justify-content-between mt-1">
    <span class="small ${(item.current_stock ?? item.real_stock ?? 0) < 10 ? 'text-danger fw-bold' : 'text-muted'}">
📦 ${item.current_stock ?? item.real_stock ?? 0}    </span>
    <span class="small text-danger">
        ⏳ ${formatExpiry(item.expiry_date)}
    </span>
</div>                        <div class="small text-secondary">MRP: ₹${item.base_price || item.mrp || 0}</div>
<button type="button" 
    class="btn btn-sm btn-success w-100 mt-2 add-item-btn"
    ${(new Date(item.expiry_date) < new Date() || (item.current_stock ?? item.real_stock ?? 0) <= 0) ? 'disabled' : ''}>
    ${(item.current_stock ?? item.real_stock ?? 0) <= 0 
        ? 'Out of Stock' 
        : (new Date(item.expiry_date) < new Date() 
            ? 'Expired' 
            : '+ Add to Order')}
</button>                   </div>
                </div>
            `;
        });
        grid.innerHTML = html;
        
        grid.querySelectorAll('.catalog-item-card').forEach(card => {
            const addBtn = card.querySelector('.add-item-btn');
  const index = card.dataset.index;
const itemData = supplierCatalogItems[index];          const addItem = () => addItemToOrder(itemData);
            addBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                addItem();
            });
            card.addEventListener('click', (e) => {
                if (e.target !== addBtn) addItem();
            });
        });
    }
    
 function addItemToOrder(catalogItem) {

    /*
    |--------------------------------------------------------------------------
    | Duplicate check by item_id
    |--------------------------------------------------------------------------
    | Same medicine again = quantity increase
    | NOT new row
    |--------------------------------------------------------------------------
    */

    const existingIndex = orderItems.findIndex(
        x => String(x.item_id) === String(catalogItem.item_id)
    );

    if (existingIndex !== -1) {

        orderItems[existingIndex].quantity += 1;

        const qty = orderItems[existingIndex].quantity;
        const rate = parseFloat(orderItems[existingIndex].rate || 0);
        const gst = parseFloat(orderItems[existingIndex].gst || 0);

        const subtotal = rate * qty;
        const gstAmount = (subtotal * gst) / 100;
        const finalTotal = subtotal + gstAmount;

        orderItems[existingIndex].subtotal = subtotal;
        orderItems[existingIndex].gst_amount = gstAmount;
        orderItems[existingIndex].total = finalTotal;

        renderOrderItemsTable();

        showRightToast("Quantity increased (same item)");

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | New item create
    |--------------------------------------------------------------------------
    */

    const ptr = parseFloat(
        catalogItem.retailer_price ||
        catalogItem.purchase_price ||
        0
    );

    const gst = parseFloat(
        catalogItem.gst_percent || 0
    );

    const qty = 1;

    const subtotal = ptr * qty;
    const gstAmount = (subtotal * gst) / 100;
    const finalTotal = subtotal + gstAmount;

    const newItem = {
        rowId: rowIdCounter++,
        catalog_id: catalogItem.id,
        item_id: catalogItem.item_id,
        name: catalogItem.item?.name || 'Medicine',
        pack_type: catalogItem.item?.pack_type || '',
        unit: catalogItem.item?.unit || '',
        number_of_units: catalogItem.item?.number_of_units || 1,

        rate: ptr,
        mrp: catalogItem.base_price || catalogItem.mrp || 0,
        gst: gst,
        stock: catalogItem.current_stock ?? catalogItem.real_stock ?? 0,

        quantity: qty,
        subtotal: subtotal,
        gst_amount: gstAmount,
        total: finalTotal
    };

    orderItems.push(newItem);

    renderOrderItemsTable();
}   
    function renderOrderItemsTable() {
        const tbody = document.getElementById('orderItemsBody');
        if (!tbody) return;
        
   if (orderItems.length === 0) {
    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="text-center text-muted py-4">
                No items added. Click on products above to add.
            </td>
        </tr>
    `;

    updateFinalTotals();
    return;
}
        
        let html = '';
        orderItems.forEach((item, idx) => {
            html += `
                <tr class="item-main-row">
                <td>
    ${escapeHtml(item.name)}

    <div class="small text-primary fw-semibold mt-1">
        📦 1 ${item.pack_type || 'Pack'} of 
        ${item.number_of_units || 1} 
        ${item.unit || 'Unit'}
    </div>

    <input type="hidden" 
        name="items[${idx}][item_id]" 
        value="${item.item_id}">

    <input type="hidden" 
        name="items[${idx}][supplier_item_catalog_id]" 
        value="${item.catalog_id}">
</td>
                    <td>
<input type="number" 
    name="items[${idx}][quantity]" 
    class="form-control qty-input" 
    value="${item.quantity}" 
    min="1" 
    max="${item.stock}" 
    step="1"
    data-rowidx="${idx}" 
    style="width: 90px;">                    </td>
                    <td>
                        <input type="number" name="items[${idx}][rate]" class="form-control rate-input" value="${item.rate}" step="0.01" data-rowidx="${idx}" readonly style="background:#f8f9fa;">
                    </td>
                    <td>
                        <input type="text" class="form-control" value="${item.mrp}" readonly style="background:#f8f9fa;">
                    </td>
                    <td>
                        <input type="number" name="items[${idx}][gst_percent]" class="form-control" value="${item.gst}" step="0.01" readonly style="background:#f8f9fa;">
                    </td>
                    <td>
                        <input type="text" class="form-control total-field total-amount" value="${item.total.toFixed(2)}" readonly>
                    </td>
             <td class="text-center">
    <button
        type="button"
        class="remove-row-btn"
        data-rowidx="${idx}"
        title="Remove Item"
    >
        🗑
    </button>
</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
        
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', function() {
                const rowIdx = parseInt(this.dataset.rowidx);
let newQty = parseFloat(this.value) || 1;
const maxStock = orderItems[rowIdx].stock || 0;

if (newQty > maxStock) {
 showRightToast(`Only ${maxStock} stock available`);
    newQty = maxStock;
    this.value = maxStock;
}

if (newQty < 1) {
    newQty = 1;
    this.value = 1;
}                if (orderItems[rowIdx]) {
             orderItems[rowIdx].quantity = newQty;

const subtotal = orderItems[rowIdx].rate * newQty;
const gstAmount = (subtotal * orderItems[rowIdx].gst) / 100;
const finalTotal = subtotal + gstAmount;

orderItems[rowIdx].subtotal = subtotal;
orderItems[rowIdx].gst_amount = gstAmount;
orderItems[rowIdx].total = finalTotal;

renderOrderItemsTable();
}
            });
        });
        
        document.querySelectorAll('.remove-row-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const rowIdx = parseInt(this.dataset.rowidx);
                orderItems.splice(rowIdx, 1);
                renderOrderItemsTable();
            });
        });
        updateFinalTotals();
    }
    
    function changeSupplier() {
        selectedSupplierData = null;
        supplierCatalogItems = [];
        orderItems = [];
        rowIdCounter = 0;
        document.getElementById('selectedSupplierInfo').style.display = 'none';
        document.getElementById('itemsSection').style.display = 'none';
        document.querySelectorAll('.supplier-select-card').forEach(c => c.classList.remove('selected-supplier'));
        
     showRightToast("Supplier changed, select again");
    }
    
    document.getElementById('purchaseForm')?.addEventListener('submit', function(e) {
        if (!selectedMedicineId) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'No Medicine', text: 'Please search and select a medicine first.' });
            return;
        }
        if (!selectedSupplierData) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'No Supplier', text: 'Please select a supplier for the medicine.' });
            return;
        }
        if (orderItems.length === 0) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'No Items', text: 'Please add at least one item to the order.' });
            return;
        }
        
        let supplierInput = document.createElement('input');
        supplierInput.type = 'hidden';
        supplierInput.name = 'supplier_id';
        supplierInput.value = selectedSupplierData.supplier_id ?? selectedSupplierData.id;
        this.appendChild(supplierInput);
    });
    
    document.getElementById('changeSupplierBtn')?.addEventListener('click', changeSupplier);
    
    const medicineSelect = document.getElementById('medicineSearch');
    if (medicineSelect) {
        const tomSelect = new TomSelect(medicineSelect, {
            valueField: 'value',
            labelField: 'text',
            searchField: ['text'],
            placeholder: '🔍 Search for a medicine...',
            allowEmptyOption: true,
            create: false,
            render: {
                option: function(data, escape) {
                    const imageUrl = data.image || '';
                    const imageHtml = imageUrl ? `<img src="${imageUrl}" style="width: 30px; height: 30px; object-fit: cover; border-radius: 8px; margin-right: 10px;">` : '';
                    return `<div class="d-flex align-items-center">${imageHtml}<span>${escape(data.text)}</span></div>`;
                },
                item: function(data, escape) {
                    return `<div>${escape(data.text)}</div>`;
                }
            }
        });
        
        tomSelect.on('change', async function(value) {
            if (!value) {
                selectedMedicineId = null;
                selectedMedicineName = null;
                document.getElementById('suppliersSection').style.display = 'none';
                document.getElementById('selectedSupplierInfo').style.display = 'none';
                document.getElementById('itemsSection').style.display = 'none';
                return;
            }
            
            selectedMedicineId = value;
            const selectedOption = tomSelect.options[value];
            selectedMedicineName = selectedOption?.text || 'Medicine';
            
            selectedSupplierData = null;
            supplierCatalogItems = [];
            orderItems = [];
            rowIdCounter = 0;
            document.getElementById('selectedSupplierInfo').style.display = 'none';
            document.getElementById('itemsSection').style.display = 'none';
            
            await loadSuppliersForMedicine(selectedMedicineId, selectedMedicineName);
        });
    }
})();

@if(isset($prefilledCatalog) && $prefilledCatalog)
document.addEventListener('DOMContentLoaded', function () {

    const itemId = "{{ $prefilledCatalog->item_id }}";
    const supplierName = "{{ $prefilledCatalog->supplier->name ?? '' }}";
    const medicineName = "{{ $prefilledCatalog->item->name ?? '' }}";

    /*
    |--------------------------------------------------------------------------
    | STEP 1 → Auto select medicine
    |--------------------------------------------------------------------------
    */

    setTimeout(function () {
        const medicineSelect = document.getElementById('medicineSearch');

        if (medicineSelect && medicineSelect.tomselect) {
            medicineSelect.tomselect.setValue(itemId);
        }
    }, 500);

    /*
    |--------------------------------------------------------------------------
    | STEP 2 → Auto click supplier
    |--------------------------------------------------------------------------
    */

    setTimeout(function () {
        document.querySelectorAll('.supplier-select-card').forEach(function(card) {
            if (card.innerText.includes(supplierName)) {
                card.click();
            }
        });
    }, 2000);

    /*
    |--------------------------------------------------------------------------
    | STEP 3 → Auto add item
    |--------------------------------------------------------------------------
    */

    setTimeout(function () {
        document.querySelectorAll('.catalog-item-card').forEach(function(card) {
            const addBtn = card.querySelector('.add-item-btn');

            if (
                card.innerText.includes(medicineName) &&
                addBtn
            ) {
                addBtn.click();
            }
        });
    }, 4000);

});
@endif

</script>
@endpush
