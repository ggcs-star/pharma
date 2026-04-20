@extends('layouts.master')

@section('title', 'Create Purchase Order')

@section('content')

<div class="container-fluid px-4 py-3">
    <!-- Modern Dashboard Card -->
    <div class="dashboard-card">
        <!-- Header -->
        <div class="card-header-custom">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="pharma-badge mb-2 d-inline-block">
                        <i class="bi bi-capsule me-1"></i> B2B Pharma Distributor
                    </span>
                    <h2 class="fw-bold mb-1" style="color: #0f172a;">
                        <i class="bi bi-cart-plus me-2" style="color: #198754;"></i>Create Purchase Order
                    </h2>
                    <p class="text-secondary small mb-0">Select medicine, choose best supplier, build your order</p>
                </div>
            </div>
        </div>
        
        <!-- Form Body -->
        <div class="p-4 p-lg-5 pt-0">
            <form method="POST" action="{{ route('purchase-orders.store') }}" id="purchaseForm">
                @csrf
                
                <div class="mb-3">
                    <label>Order Date</label>
                    <input type="date" name="order_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                
                <!-- Items Table -->
                <div class="table-responsive mb-4">
                    <table class="table items-table" id="items_table">
                        <thead>
                            <tr>
                                <th style="width: 28%;">Medicine Item</th>
                                <th style="width: 8%;">Qty</th>
                                <th style="width: 12%;">PTR (Rate)</th>
                                <th style="width: 12%;">MRP</th>
                                <th style="width: 10%;">GST%</th>
                                <th style="width: 12%;">Total</th>
                                <th style="width: 8%;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- Rows dynamically added -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Actions -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <button type="button" id="addRowBtn" class="btn btn-add-item">
                        <i class="bi bi-plus-circle me-1"></i> Add Medicine Item
                    </button>
                    <button type="submit" class="btn btn-save text-white">
                        <i class="bi bi-check2-circle me-1"></i> Save Purchase Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<!-- TomSelect CSS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">

<style>
    * {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    .dashboard-card {
        background: #ffffff;
        border-radius: 32px;
        box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.08);
        border: none;
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
    }
    
    .items-table thead th {
        background: #fafcff;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #5a6e8a;
        border-bottom: 2px solid #eef2f6;
        padding: 14px 12px;
    }
    
    .items-table tbody td {
        vertical-align: middle;
        padding: 12px 8px;
        border-bottom: 1px solid #f0f3f8;
    }
    
    .form-control, .ts-control {
        border-radius: 14px;
        border: 1.5px solid #e2e8f0;
        padding: 8px 12px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    
    .form-control:focus, .ts-wrapper.focus .ts-control {
        border-color: #198754;
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.12);
        outline: none;
    }
    
    .ts-wrapper {
        min-width: 220px;
    }
    
    .ts-dropdown {
        z-index: 1060 !important;
        border-radius: 16px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
    }
    
    .supplier-list {
        display: flex;
        flex-wrap: wrap;
        gap: 18px;
        padding: 20px 12px;
        max-height: 420px;
        overflow-y: auto;
        background: #fefefe;
    }
    
    .supplier-card {
        background: white;
        border-radius: 24px;
        border: 2px solid #edf2f7;
        padding: 16px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.2, 0, 0, 1);
        width: 310px;
        flex-shrink: 0;
        position: relative;
    }
    
    .supplier-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.15);
        border-color: #cbd5e1;
    }
    
    .supplier-card.selected-supplier {
        border: 2px solid #198754;
        background: linear-gradient(135deg, #f0fff4 0%, #fafef8 100%);
        box-shadow: 0 8px 20px rgba(25, 135, 84, 0.18);
    }
    
    .supplier-card.selected-supplier::after {
        content: "✓";
        position: absolute;
        top: 12px;
        right: 12px;
        background: #198754;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: bold;
    }
    
    .supplier-card.best-price-card {
        border: 2px solid #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #fffef7 100%);
    }
    
    .supplier-card.best-price-card::before {
        content: "🏆 BEST PRICE";
        position: absolute;
        top: -10px;
        left: 16px;
        background: #f59e0b;
        color: white;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 3px 12px;
        border-radius: 30px;
    }
    
    .medicine-img-card {
        width: 65px;
        height: 65px;
        object-fit: cover;
        border-radius: 16px;
        background: #f8fafc;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }
    
    .supplier-name-text {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.85rem;
    }
    
    .price-badge {
        background: #eef2ff;
        color: #1e3a8a;
        border-radius: 30px;
        padding: 3px 10px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .supplier-catalog {
        padding: 16px 12px;
        background: #fafcff;
        border-radius: 20px;
        margin-top: 12px;
        border: 1px solid #eef2f8;
    }
    
    .catalog-item {
        cursor: pointer;
        transition: all 0.2s ease;
        border-radius: 16px;
        border: 1px solid #eef2f8;
        background: white;
        padding: 12px;
    }
    
    .catalog-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-color: #198754;
    }
    
    .btn-add-item {
        background: white;
        border: 2px solid #d0d9e8;
        border-radius: 40px;
        padding: 10px 28px;
        font-weight: 600;
        color: #1f6e43;
        transition: all 0.2s;
    }
    
    .btn-add-item:hover {
        background: #e9f5ef;
        border-color: #198754;
        transform: translateY(-2px);
    }
    
    .btn-save {
        background: linear-gradient(135deg, #198754 0%, #0f5c3a 100%);
        border: none;
        border-radius: 40px;
        padding: 11px 36px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
        color: white;
    }
    
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(25, 135, 84, 0.4);
        color: white;
    }
    
    .remove-row-btn {
        background: transparent;
        border: none;
        color: #94a3b8;
        width: 34px;
        height: 34px;
        border-radius: 12px;
        transition: all 0.2s;
    }
    
    .remove-row-btn:hover {
        background: #fee2e2;
        color: #dc2626;
    }
    
    .loader-spinner {
        display: inline-block;
        width: 1.2rem;
        height: 1.2rem;
        border: 2px solid #e2e8f0;
        border-top-color: #198754;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .fade-in-up {
        animation: fadeInUp 0.3s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .supplier-list::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    
    .supplier-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .supplier-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    
    .total-field {
        background: #f8fafc;
        font-weight: 700;
        color: #198754;
    }
    
    @media (max-width: 768px) {
        .supplier-card {
            width: calc(100% - 10px);
        }
        .card-header-custom {
            padding: 1.25rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function() {
    'use strict';
    
    let rowCounter = 0;
    
    // Helper function to get image URL with proper S3 handling
    function getImageUrl(item) {
        if (!item) return 'https://placehold.co/600x400?text=Medicine+Image';
        
        // Check if image_url exists and is a valid URL
        if (item.image_url) {
            // If it's a full URL (starts with http or https)
            if (item.image_url.startsWith('http://') || item.image_url.startsWith('https://')) {
                return item.image_url;
            }
            // If it's a relative path, you might need to prepend your S3 base URL
            // Adjust this according to your S3 configuration
            if (item.image_url.startsWith('/storage/') || item.image_url.startsWith('storage/')) {
                return item.image_url;
            }
            return item.image_url;
        }
        
        // Check for main_image as fallback
        if (item.main_image) {
            if (item.main_image.startsWith('http://') || item.main_image.startsWith('https://')) {
                return item.main_image;
            }
            return item.main_image;
        }
        
        return 'https://placehold.co/600x400?text=Medicine+Image';
    }
    
    function findBestPriceSupplier(suppliers) {
        if (!suppliers || suppliers.length === 0) return null;
        return suppliers.reduce((best, current) => {
            const currentPrice = current.purchase_price || current.retailer_price || Infinity;
            const bestPrice = best.purchase_price || best.retailer_price || Infinity;
            return currentPrice < bestPrice ? current : best;
        }, suppliers[0]);
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
    
    function calculateTotal(rowElement) {
        const qty = parseFloat(rowElement.querySelector('.qty')?.value) || 0;
        const rate = parseFloat(rowElement.querySelector('.rate')?.value) || 0;
        const totalField = rowElement.querySelector('.total');
        if (totalField) {
            totalField.value = (qty * rate).toFixed(2);
        }
    }
    
    function renderSupplierCatalog(catalog, supplierRow) {
        const container = supplierRow.querySelector('.supplier-catalog');
        if (!container) return;
    
        if (!catalog || catalog.length === 0) {
            container.innerHTML = '<div class="text-muted p-3 text-center">No catalog items available</div>';
            return;
        }
    
        let html = '<h6 class="mb-3 mt-2 fw-semibold">Supplier Items</h6><div class="row g-3">';
    
        catalog.forEach(data => {
            let item = data.item;
            const imgUrl = getImageUrl(item);
            
            html += `
                <div class="col-md-3 col-sm-6">
                    <div class="catalog-item" data-item='${JSON.stringify(data)}'>
                        <img src="${imgUrl}" class="img-fluid rounded mb-2" 
                             style="height:80px;width:100%;object-fit:cover;"
                             onerror="this.src='https://placehold.co/600x400?text=Medicine+Image'">
                        <div class="small fw-semibold">${escapeHtml(item?.name || '')}</div>
                        <div class="text-success fw-bold">₹${data.purchase_price || 0}</div>
                        <div class="small text-muted">GST: ${data.gst_percent || 0}%</div>
                    </div>
                </div>
            `;
        });
    
        html += '</div>';
        container.innerHTML = html;
    
        container.querySelectorAll('.catalog-item').forEach(card => {
            card.addEventListener('click', function(e) {
                e.stopPropagation();
    
                const data = JSON.parse(this.dataset.item);
    
                let itemRow = supplierRow.previousElementSibling;
    
                if (!itemRow) return;
    
                let alreadyFilled = itemRow.querySelector('.catalog_id').value;
    
                if (alreadyFilled) {
                    addNewRow();
    
                    const rows = document.querySelectorAll('#itemsTableBody .item-main-row');
                    itemRow = rows[rows.length - 1];
                }
    
                itemRow.querySelector('.catalog_id').value = data.id;
                itemRow.querySelector('.rate').value = data.purchase_price;
                itemRow.querySelector('.mrp').value = data.base_price;
                itemRow.querySelector('.gst').value = data.gst_percent;
    
                const select = itemRow.querySelector('.item-select');
                if (select && select.tomselect) {
                    select.tomselect.setValue(String(data.item_id));
                }
    
                const qty = itemRow.querySelector('.qty')?.value || 1;
                itemRow.querySelector('.total').value = (qty * data.purchase_price).toFixed(2);
            });
        });
    }
    
    function generateSupplierCards(suppliers, currentItemId) {
        if (!suppliers || suppliers.length === 0) {
            return `<div class="text-center text-muted w-100 p-4">
                        <i class="bi bi-building-slash fs-1"></i>
                        <p class="mt-2 mb-0">No suppliers available</p>
                    </div>`;
        }
        
        const bestSupplier = findBestPriceSupplier(suppliers);
        const bestSupplierId = bestSupplier ? bestSupplier.id : null;
        
        let html = '';
        suppliers.forEach(supplier => {
            const isBestPrice = (bestSupplierId === supplier.id);
            const imageUrl = getImageUrl(supplier.item);
            
            html += `
                <div class="supplier-card ${isBestPrice ? 'best-price-card' : ''}"
                     data-catalog-id="${supplier.id}"
                     data-item-id="${supplier.item_id}"
                     data-rate="${supplier.purchase_price || supplier.retailer_price || 0}"
                     data-mrp="${supplier.base_price || supplier.mrp || 0}"
                     data-gst="${supplier.gst_percent || 0}"
                     data-supplier-id="${supplier.supplier_id ?? supplier.id}"
                     data-supplier-data='${JSON.stringify(supplier)}'>
                    <div class="d-flex gap-3">
                        <img src="${imageUrl}" 
                             class="medicine-img-card" 
                             alt="${escapeHtml(supplier.item?.name)}"
                             onerror="this.src='https://placehold.co/600x400?text=Medicine+Image'">
                        <div class="flex-grow-1">
                            <div class="fw-bold mb-1">${escapeHtml(supplier.item?.name)}</div>
                            <div class="supplier-name-text mb-2">
                                <i class="bi bi-building me-1"></i>${escapeHtml(supplier.supplier?.name)}
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="price-badge">💰 PTR: ₹${supplier.purchase_price || supplier.retailer_price || 0}</span>
                                <span class="price-badge">🏷️ MRP: ₹${supplier.base_price || supplier.mrp || 0}</span>
                                <span class="price-badge">📊 GST: ${supplier.gst_percent || 0}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        return html;
    }
    
    async function fetchAndRenderSuppliers(itemId, supplierRow, itemRow) {
        const listContainer = supplierRow.querySelector('.supplier-list');
        if (!listContainer) return;
        
        listContainer.innerHTML = `<div class="text-center p-4"><div class="loader-spinner"></div><p class="mt-2">Loading suppliers...</p></div>`;
        supplierRow.style.display = 'table-row';
        
        try {
            const response = await fetch(`/item/suppliers?item_id=${itemId}`);
            if (!response.ok) throw new Error('Network error');
            const suppliers = await response.json();
            
            if (!suppliers || suppliers.length === 0) {
                listContainer.innerHTML = '<div class="text-center text-muted p-4">No suppliers found</div>';
                return;
            }
            
            listContainer.innerHTML = generateSupplierCards(suppliers, itemId);
            
            const cards = listContainer.querySelectorAll('.supplier-card');
            cards.forEach(card => {
                card.addEventListener('click', async function(e) {
                    e.stopPropagation();
                    cards.forEach(c => c.classList.remove('selected-supplier'));
                    this.classList.add('selected-supplier');
                    
                    const rate = this.dataset.rate;
                    const mrp = this.dataset.mrp;
                    const gst = this.dataset.gst;
                    const catalogId = this.dataset.catalogId;
                    const itemIdVal = this.dataset.itemId;
                    const supplierId = this.dataset.supplierId;
                    const supplierData = JSON.parse(this.dataset.supplierData);
                    
                    itemRow.querySelector('.rate').value = rate;
                    itemRow.querySelector('.mrp').value = mrp;
                    itemRow.querySelector('.gst').value = gst;
                    itemRow.querySelector('.catalog_id').value = catalogId;
                    calculateTotal(itemRow);
                    
                    if (supplierId) {
                        try {
                            const catRes = await fetch(`/api/supplier-items/${supplierId}`);
                            if (catRes.ok) {
                                const catalog = await catRes.json();
                                renderSupplierCatalog(catalog, supplierRow);
                            }
                        } catch(e) {
                            console.log('Catalog error:', e);
                        }
                    }
                });
            });
        } catch (error) {
            console.error('Error:', error);
            listContainer.innerHTML = '<div class="text-center text-danger p-4">Error loading suppliers</div>';
        }
    }
    
    function createNewRow(rowIdx) {
        let optionsHtml = '<option value="">Select Medicine</option>';
        @foreach($items as $item)
            optionsHtml += `<option value="{{ $item->id }}" data-image="{{ $item->image_url ?? $item->main_image ?? '' }}">{{ $item->name }}</option>`;
        @endforeach
        
        return `
        <tr class="item-main-row fade-in-up">
            <td>
                <select name="items[${rowIdx}][item_id]" class="form-control item-select" required>
                    ${optionsHtml}
                </select>
                <input type="hidden" name="items[${rowIdx}][supplier_item_catalog_id]" class="catalog_id">
            </td>
            <td><input type="number" name="items[${rowIdx}][quantity]" class="form-control qty" value="1" min="1" required></td>
            <td><input type="number" name="items[${rowIdx}][rate]" class="form-control rate" step="0.01" placeholder="Select supplier" required></td>
            <td><input type="text" class="form-control mrp" readonly placeholder="MRP"></td>
            <td><input type="number" name="items[${rowIdx}][gst_percent]" class="form-control gst" step="0.01" placeholder="GST%"></td>
            <td><input type="text" class="form-control total total-field" readonly placeholder="0.00"></td>
            <td class="text-center"><button type="button" class="btn remove-row-btn"><i class="bi bi-trash3"></i></button></td>
        </tr>
        <tr class="supplier-row" style="display: none;">
            <td colspan="7">
                <div class="supplier-list"></div>
                <div class="supplier-catalog mt-3"></div>
            </td>
        </tr>
        `;
    }
    
    function addNewRow() {
        const tbody = document.getElementById('itemsTableBody');
        const html = createNewRow(rowCounter);
        tbody.insertAdjacentHTML('beforeend', html);
        
        const rows = tbody.querySelectorAll('.item-main-row');
        const newItemRow = rows[rows.length - 1];
        const newSupplierRow = newItemRow.nextElementSibling;
        
        const selectEl = newItemRow.querySelector('.item-select');
        if (selectEl) {
            const tsInstance = new TomSelect(selectEl, {
                valueField: 'value',
                labelField: 'text',
                searchField: ['text'],
                placeholder: '🔍 Search medicine...',
                allowEmptyOption: true,
                create: false,
                render: {
                    option: function(data, escape) {
                        const imageUrl = data.image || '';
                        const imageHtml = imageUrl ? 
                            `<img src="${imageUrl}" style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px; margin-right: 8px;">` : 
                            '';
                        return `<div class="d-flex align-items-center">
                                    ${imageHtml}
                                    <span>${escape(data.text)}</span>
                                </div>`;
                    },
                    item: function(data, escape) {
                        return `<div>${escape(data.text)}</div>`;
                    }
                }
            });
            
            tsInstance.on('change', async function(value) {
                if (!value) return;
                newItemRow.querySelector('.rate').value = '';
                newItemRow.querySelector('.mrp').value = '';
                newItemRow.querySelector('.gst').value = '';
                newItemRow.querySelector('.catalog_id').value = '';
                calculateTotal(newItemRow);
                await fetchAndRenderSuppliers(value, newSupplierRow, newItemRow);
            });
        }
        
        const qtyInput = newItemRow.querySelector('.qty');
        const rateInput = newItemRow.querySelector('.rate');
        qtyInput?.addEventListener('input', () => calculateTotal(newItemRow));
        rateInput?.addEventListener('input', () => calculateTotal(newItemRow));
        
        const removeBtn = newItemRow.querySelector('.remove-row-btn');
        removeBtn?.addEventListener('click', () => {
            if (selectEl?.tomselect) selectEl.tomselect.destroy();
            newItemRow.remove();
            newSupplierRow.remove();
        });
        
        rowCounter++;
    }
    
    function initializeFirstRow() {
        const tbody = document.getElementById('itemsTableBody');
        tbody.innerHTML = '';
        rowCounter = 0;
        addNewRow();
        rowCounter = 1;
    }
    
    document.getElementById('purchaseForm')?.addEventListener('submit', function(e) {
        const itemRows = document.querySelectorAll('#itemsTableBody .item-main-row');
        let hasValidItem = false;
        
        for (let row of itemRows) {
            const itemId = row.querySelector('.item-select')?.value;
            const catalogId = row.querySelector('.catalog_id')?.value;
            const quantity = row.querySelector('.qty')?.value;
            
            if (itemId && catalogId && quantity && parseInt(quantity) > 0) {
                hasValidItem = true;
                break;
            }
        }
        
        if (!hasValidItem) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Cannot Save',
                text: 'Please select a medicine and choose a supplier before saving.',
                confirmButtonColor: '#198754'
            });
        }
    });
    
    document.getElementById('addRowBtn')?.addEventListener('click', addNewRow);
    document.addEventListener('DOMContentLoaded', initializeFirstRow);
})();
</script>
@endpush