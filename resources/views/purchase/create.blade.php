{{-- resources/views/purchases/create.blade.php --}}
@extends('layouts.master')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        {{-- Header --}}
        <div class="card-header bg-gradient-primary text-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 fw-bold">
                        <i class="fas fa-shopping-cart me-2"></i> Purchase Entry
                    </h4>
                    <p class="text-white-50 small mt-1 mb-0">Create new purchase invoice</p>
                </div>
                <div class="text-end">
                    <div class="bg-white-20 rounded-3 px-3 py-2">
                        <i class="fas fa-calendar-alt me-1"></i>
                        <span class="small" id="displayPurchaseDate">{{ date('d-m-Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('purchase.store') }}" method="POST" id="purchaseForm">
                @csrf

                {{-- Header Information --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">
                            <i class="fas fa-receipt text-primary me-1"></i> Invoice No <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="invoice_number" class="form-control" 
                               placeholder="INV-001" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">
                            <i class="fas fa-calendar-day text-primary me-1"></i> Purchase Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="purchase_date" id="purchase_date" class="form-control" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">
                            <i class="fas fa-truck text-primary me-1"></i> Supplier <span class="text-danger">*</span>
                        </label>
                        <select name="supplier_id" id="supplier_id" class="form-select" required>
                            <option value="">-- Select Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">
                            <i class="fas fa-wallet text-primary me-1"></i> Payment Type
                        </label>
                        <select name="payment_type" id="payment_type" class="form-select">
                            <option value="Pending">⏳ Pending</option>
                            <option value="Cash">💵 Cash</option>
                            <option value="UPI">📱 UPI</option>
                            <option value="Bank">🏦 Bank</option>
                        </select>
                    </div>
                </div>

                {{-- Add Item Button --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-boxes text-primary me-2"></i> Purchase Items
                        </h5>
                        <p class="text-muted small mb-0">Add products with batch details</p>
                    </div>
                    <button type="button" class="btn btn-gradient-primary rounded-pill px-4 py-2" onclick="addRow()">
                        <i class="fas fa-plus-circle me-2"></i> Add Purchase
                    </button>
                </div>

                {{-- Items Table --}}
                <div class="purchase-table-wrapper" style="position: relative;">
                    <div class="table-responsive rounded-3 border" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-hover mb-0" id="purchaseTable" style="min-width: 1800px;">
                            <thead class="bg-light sticky-top">
                                <tr class="text-center small">
                                    <th style="width: 3%;">#</th>
                                    <th style="width: 12%;">Item <span class="text-danger">*</span></th>
                                    <th style="width: 7%;">Packing</th>
                                    <th style="width: 5%;">Qty <span class="text-danger">*</span></th>
                                    <th style="width: 5%;">Free</th>
                                    <th style="width: 8%;">Batch No <span class="text-danger">*</span></th>
                                    <th style="width: 7%;">Rack</th>
                                    <th style="width: 7%;">Expiry <span class="text-danger">*</span></th>
                                    <th style="width: 6%;">MRP (₹)</th>
                                    <th style="width: 6%;">PTR (₹)</th>
                                    <th style="width: 6%;">Offline Price</th>
<th style="width: 6%;">Online Price</th>
                                    <th style="width: 5%;">GST%</th>
                                    <th style="width: 5%;">Disc%</th>
                                    <th style="width: 6%;">Disc ₹</th>
                                    <th style="width: 7%;">Taxable</th>
                                    <th style="width: 6%;">GST</th>
                                    <th style="width: 7%;">Total</th>
                                    <th style="width: 4%;"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                {{-- Totals Section --}}
                <div class="row justify-content-end mt-4">
                    <div class="col-md-5 col-lg-4">
                        <div class="card bg-gradient-light border-0 rounded-4 shadow-sm">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                    <span class="fw-semibold text-muted">Subtotal:</span>
                                    <span class="fw-bold">₹ <span id="subTotal">0.00</span></span>
                                </div>
                                <div class="mb-2">
                                    <label class="fw-semibold text-muted small mb-1">Extra Charges:</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-transparent">₹</span>
                                        <input type="number" step="0.01" name="extra_charges" id="extraCharges" 
                                               class="form-control" value="0">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-semibold text-muted small mb-1">Round Off:</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-transparent">₹</span>
                                        <input type="number" step="0.01" name="round_off" id="roundOff" 
                                               class="form-control" value="0">
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Grand Total:</span>
                                    <span class="fs-5 fw-bold text-gradient">₹ <span id="grandTotal">0.00</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                    <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">
                        <i class="fas fa-times me-2"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-gradient-success px-4 rounded-pill shadow-sm">
                        <i class="fas fa-save me-2"></i> Save Purchase
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- TomSelect CSS --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

{{-- TomSelect JS --}}
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    let rowIndex = 0;
    let isLoading = false;
    const itemCache = new Map();
    const tomSelectInstances = new Map();
    
    let allItems = @json($items);

    function addRow() {
        const tbody = document.querySelector("#purchaseTable tbody");
        const row = createEmptyRow(rowIndex);
        tbody.insertAdjacentHTML('beforeend', row);
        
        setTimeout(() => {
            const lastRow = tbody.lastElementChild;
            const itemSelect = lastRow.querySelector('.itemSelect');
            if (itemSelect) {
                initTomSelect(itemSelect);
            }
        }, 50);
        
        rowIndex++;
    }

    function createEmptyRow(index) {
        return `
            <tr data-row-index="${index}">
                <td class="text-center row-number fw-bold bg-light small">${index + 1}</td>
                <td>
                    <select name="items[${index}][item_id]" class="itemSelect form-select form-select-sm" required>
                        <option value="">-- Select --</option>
                    </select>
                    <input type="hidden" name="items[${index}][barcode]" class="barcode">
                    <input type="hidden" name="items[${index}][hsn_code]" class="hsnCode">
                    <input type="hidden" name="items[${index}][packing]" class="packingValue">
                    
                </td>
      <td>
    <div class="packingDisplay border rounded bg-light px-2 py-1 text-center small">
        <div class="fw-bold packingMain">--</div>
        <div class="text-success packingSub">--</div>
    </div>
</td>
                <td>
                    <input type="number" name="items[${index}][quantity]" class="form-control form-control-sm qty text-center" 
                           step="any" value="0" min="0" required>
                </td>
                <td>
                    <input type="number" name="items[${index}][free_quantity]" class="form-control form-control-sm freeQty text-center" 
                           step="any" value="0" min="0">
                </td>
                <td>
                    <input type="text" name="items[${index}][batch_number]" class="form-control form-control-sm batchNumber" 
                           placeholder="Batch No." required>
                </td>
                <td>
    <input type="text" 
           name="items[${index}][rack]" 
           class="form-control form-control-sm rack" 
           placeholder="Rack (A1, B2...)">
</td>
                <td>
                    <input type="date" name="items[${index}][expiry_date]" class="form-control form-control-sm expiryDate" required>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${index}][mrp]" class="form-control form-control-sm mrp text-end" value="0">
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${index}][purchase_rate]" class="form-control form-control-sm rate text-end" required>
                </td>
                <td>
    <input type="number" step="0.01"
        name="items[${index}][offline_price]"
        class="form-control form-control-sm offline_price text-end"
        value="0">
</td>

<td>
    <input type="number" step="0.01"
        name="items[${index}][online_price]"
        class="form-control form-control-sm online_price text-end"
        value="0">
</td>
                <td>
                    <input type="number" step="0.01" name="items[${index}][gst_percent]" class="form-control form-control-sm gst text-end" value="0">
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${index}][discount_percent]" class="form-control form-control-sm discP text-end" value="0" min="0" max="100">
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${index}][discount_amount]" class="form-control form-control-sm discA text-end" value="0" min="0">
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${index}][taxable_amount]" class="form-control form-control-sm taxable text-end bg-light" readonly>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${index}][gst_amount]" class="form-control form-control-sm gstAmt text-end bg-light" readonly>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${index}][total_amount]" class="form-control form-control-sm amount text-end fw-bold bg-light" readonly>
                </td>
                <td class="text-center">
                    <button type="button" onclick="removeRow(this)" class="btn btn-danger btn-sm rounded-circle" style="width: 28px; height: 28px; padding: 0;">
                        <i class="fas fa-trash-alt fa-xs"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    function initTomSelect(element) {
        const existingInstance = tomSelectInstances.get(element);
        if (existingInstance) existingInstance.destroy();

        const formattedItems = allItems.map(item => ({
            id: item.id,
            name: item.name,
            gst_percent: item.gst_percent || 0,
            hsn_code: item.hsn_code || 'N/A',
            barcode: item.barcode || '',
            rack: item.rack || '',
            mrp: item.mrp || 0,
            purchase_rate: item.purchase_rate || 0,
packing: item.packing_details?.packaging_detail || ''
        }));

        const ts = new TomSelect(element, {
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            options: formattedItems,
            maxOptions: 50,
            maxItems: 1,
            create: false,
            persist: false,
            closeAfterSelect: true,
            placeholder: 'Search item...',
            dropdownParent: 'body',
            
            render: {
                option: function(item, escape) {
                    return `
                        <div class="py-1 px-2">
                            <div class="fw-semibold small">${escape(item.name)}</div>
                            <div class="small text-muted">
                                GST: ${item.gst_percent || 0}% | HSN: ${item.hsn_code || 'N/A'}
                            </div>
                        </div>
                    `;
                },
                item: function(item, escape) {
                    return `<div class="small">${escape(item.name)}</div>`;
                }
            }
        });

        tomSelectInstances.set(element, ts);

        ts.on('change', function(value) {
            const row = element.closest('tr');
            if (value) {
                loadItemData(value, row);
                setTimeout(() => row.querySelector('.qty')?.focus(), 50);
            }
        });
    }

    function removeRow(btn) {
        const row = btn.closest('tr');
        const itemSelect = row.querySelector('.itemSelect');
        if (itemSelect) {
            const ts = tomSelectInstances.get(itemSelect);
            if (ts) {
                ts.destroy();
                tomSelectInstances.delete(itemSelect);
            }
        }
        row.remove();
        refreshRowNumbers();
        calculateTotal();
    }

    function refreshRowNumbers() {
        document.querySelectorAll('#purchaseTable tbody tr').forEach((tr, i) => {
            tr.querySelector('.row-number').innerText = i + 1;
            tr.setAttribute('data-row-index', i);
            tr.querySelectorAll('[name]').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/items\[\d+\]/, `items[${i}]`));
                }
            });
        });
        rowIndex = document.querySelectorAll('#purchaseTable tbody tr').length;
    }

    async function loadItemData(itemId, row) {
        if (!itemId) return;
        
        if (itemCache.has(itemId)) {
            populateItemData(itemCache.get(itemId), row);
            return;
        }
        
        try {
            const response = await fetch(`/purchase/get-item/${itemId}`);
            const data = await response.json();
            itemCache.set(itemId, data);
            populateItemData(data, row);
        } catch (error) {
            console.error('Error:', error);
        }
    }
// Packing ko sirf 10x10 ki jagah
// "1 Strip = 10 Tablets" format me dikhana hai

function populateItemData(data, row) {
    if (!data) return;

    row.querySelector('.gst').value = data.gst_percent || 0;
    row.querySelector('.barcode').value = data.barcode || '';
    row.querySelector('.rack').value = data.rack || '';
    row.querySelector('.hsnCode').value = data.hsn_code || '';

    // ===== PACKING DISPLAY FIX =====

    let packing = data.packing || '';

    let mainText = packing;
    let subText = '';

    // Example:
    // 10x10  =>  1 Strip = 10 Tablets

    if (packing.includes('x')) {
        let parts = packing.split('x');

        if (parts.length >= 2) {
            let stripSize = parseInt(parts[1]) || 0;

            mainText = packing;
            subText = `1 Strip = ${stripSize} Tablets`;
        }
    }

    row.querySelector('.packingValue').value = packing;

    // IMPORTANT:
    // .value nahi use karna
    // innerText use karna hai

    row.querySelector('.packingMain').innerText = mainText;
    row.querySelector('.packingSub').innerText = subText;

    // ===============================

    if (data.mrp) row.querySelector('.mrp').value = data.mrp;
    if (data.purchase_rate) row.querySelector('.rate').value = data.purchase_rate;
row.querySelector('.offline_price').value = data.mrp || 0;
row.querySelector('.online_price').value = data.mrp || 0;
    calculateRowTotal(row);
    calculateTotal();
}    function calculateRowTotal(row) {
        const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
        const rate = parseFloat(row.querySelector('.rate')?.value) || 0;
        const gst = parseFloat(row.querySelector('.gst')?.value) || 0;
        const discP = parseFloat(row.querySelector('.discP')?.value) || 0;
        const discA = parseFloat(row.querySelector('.discA')?.value) || 0;
        
        const basic = qty * rate;
        const discountPercentAmount = (basic * discP) / 100;
        const totalDiscount = discountPercentAmount + discA;
        const finalDiscount = Math.min(totalDiscount, basic);
        const taxable = basic - finalDiscount;
        const gstAmount = (taxable * gst) / 100;
        const total = taxable + gstAmount;
        
        row.querySelector('.taxable').value = taxable.toFixed(2);
        row.querySelector('.gstAmt').value = gstAmount.toFixed(2);
        row.querySelector('.amount').value = total.toFixed(2);
        
        return total;
    }

    function calculateTotal() {
        let subtotal = 0;
        document.querySelectorAll('.amount').forEach(el => subtotal += parseFloat(el.value) || 0);
        
        const extra = parseFloat(document.getElementById('extraCharges')?.value) || 0;
        const round = parseFloat(document.getElementById('roundOff')?.value) || 0;
        const grand = subtotal + extra + round;
        
        document.getElementById('subTotal').innerText = subtotal.toFixed(2);
        document.getElementById('grandTotal').innerText = grand.toFixed(2);
    }

    document.addEventListener('input', function(e) {
        const row = e.target.closest('tr');
        if (!row) return;
        
        if (e.target.matches('.qty, .freeQty, .rate, .gst, .discP, .discA')) {
            if (e.target.classList.contains('discP')) {
                const val = parseFloat(e.target.value) || 0;
                if (val > 100) e.target.value = 100;
                if (val > 0) row.querySelector('.discA').value = 0;
            }
            if (e.target.classList.contains('discA')) {
                const val = parseFloat(e.target.value) || 0;
                if (val > 0) row.querySelector('.discP').value = 0;
            }
            calculateRowTotal(row);
            calculateTotal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === "Enter" && !e.target.closest('.ts-control')) {
            e.preventDefault();
            const inputs = [...document.querySelectorAll('#purchaseForm input:not([readonly]), #purchaseForm select:not(.itemSelect)')];
            const idx = inputs.indexOf(document.activeElement);
            if (idx > -1 && idx < inputs.length - 1) inputs[idx + 1].focus();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        addRow();
        
        document.getElementById('extraCharges')?.addEventListener('input', calculateTotal);
        document.getElementById('roundOff')?.addEventListener('input', calculateTotal);
        
        document.getElementById('purchaseForm').addEventListener('submit', function(e) {
            let errors = [];
            const rows = document.querySelectorAll('#purchaseTable tbody tr');
            
            rows.forEach((row, i) => {
                if (!row.querySelector('.itemSelect')?.value) errors.push(`Row ${i+1}: Select an item`);
                if ((parseFloat(row.querySelector('.qty')?.value) || 0) <= 0) errors.push(`Row ${i+1}: Qty > 0 required`);
                if (!row.querySelector('.batchNumber')?.value) errors.push(`Row ${i+1}: Batch number required`);
                if (!row.querySelector('.expiryDate')?.value) errors.push(`Row ${i+1}: Expiry date required`);
                if ((parseFloat(row.querySelector('.rate')?.value) || 0) <= 0) errors.push(`Row ${i+1}: Rate > 0 required`);
            });
            
            if (errors.length) {
                e.preventDefault();
                alert(errors.join('\n'));
            }
        });
    });
</script>

<style>
    .ts-wrapper { min-width: 150px; }
    .ts-control { 
        border: 1px solid #e2e8f0 !important; 
        padding: 2px 6px !important; 
        min-height: 29px !important; 
        font-size: 0.8rem !important; 
    }
    .ts-dropdown { 
        z-index: 99999 !important; 
        max-height: 250px !important; 
        min-width: 250px !important; 
    }
    .ts-dropdown .option { padding: 5px 10px !important; }
    
    .bg-gradient-primary { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); }
    .btn-gradient-primary { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none; color: white; }
    .btn-gradient-primary:hover { background: linear-gradient(135deg, #163158 0%, #1f3d6e 100%); color: white; }
    .btn-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none; color: white; }
    .btn-gradient-success:hover { background: linear-gradient(135deg, #0d7a6f 0%, #2bc46a 100%); color: white; }
    .text-gradient { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .bg-gradient-light { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); }
    .bg-white-20 { background: rgba(255,255,255,0.2); }
    
    .form-control, .form-select { font-size: 0.85rem; padding: 0.35rem 0.6rem; }
    .form-control-sm, .form-select-sm { font-size: 0.75rem; padding: 0.2rem 0.3rem; }
    
    .table { font-size: 0.75rem; }
    .table th { font-weight: 600; background-color: #f8fafc; white-space: nowrap; padding: 0.5rem 0.25rem; }
    .table td { padding: 0.25rem; vertical-align: middle; }
    .table input, .table select { font-size: 0.75rem; padding: 0.15rem 0.25rem; }
    
    .sticky-top { position: sticky; top: 0; z-index: 10; }
    
    .rounded-circle { width: 24px !important; height: 24px !important; }
    .rounded-pill { border-radius: 50px !important; }
    
    input[readonly] { background-color: #f8fafc; }
    
    #purchaseTable tbody tr:hover { background-color: #f8fafc; }
</style>
@endsection