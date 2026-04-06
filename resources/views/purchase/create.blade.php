{{-- resources/views/purchases/create.blade.php --}}
@extends('layouts.master')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        {{-- Header --}}
        <div class="card-header bg-gradient-primary text-white py-4 border-0">
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
                        <span class="small">{{ date('d-m-Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('purchase.store') }}" method="POST" id="purchaseForm">
                @csrf

                {{-- Header Information --}}
                <div class="row g-4 mb-5">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-receipt text-primary me-1"></i> Invoice No <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="invoice_number" class="form-control form-control-lg" 
                               placeholder="INV-001" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calendar-day text-primary me-1"></i> Invoice Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="purchase_date" id="purchase_date" class="form-control form-control-lg" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-truck text-primary me-1"></i> Supplier <span class="text-danger">*</span>
                        </label>
                        <select name="supplier_id" id="supplier_id" class="form-select form-select-lg" required>
                            <option value="">-- Select Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-wallet text-primary me-1"></i> Payment Type
                        </label>
                        <select name="payment_type" id="payment_type" class="form-select form-select-lg">
                            <option value="Pending">⏳ Pending</option>
                            <option value="Cash">💵 Cash</option>
                            <option value="UPI">📱 UPI</option>
                            <option value="Bank">🏦 Bank</option>
                        </select>
                    </div>
                </div>

                {{-- Add Item Button --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
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
                <div class="table-responsive mb-4 rounded-3 border">
                    <table class="table table-bordered table-hover align-middle mb-0" id="purchaseTable">
                        <thead class="bg-light">
                            <tr class="text-center">
                                <th width="3%" class="py-3">#</th>
                                <th width="14%" class="py-3">Item <span class="text-danger">*</span></th>
                                <th width="6%" class="py-3">Qty <span class="text-danger">*</span></th>
                                <th width="6%" class="py-3">Free</th>
                                <th width="10%" class="py-3">Batch No <span class="text-danger">*</span></th>
                                <th width="8%" class="py-3">Expiry <span class="text-danger">*</span></th>
                                <th width="7%" class="py-3">MRP (₹)</th>
                                <th width="7%" class="py-3">PTR (₹)</th>
                                <th width="6%" class="py-3">GST%</th>
                                <th width="6%" class="py-3">Disc%</th>
                                <th width="7%" class="py-3">Disc ₹</th>
                                <th width="8%" class="py-3">Taxable</th>
                                <th width="8%" class="py-3">GST</th>
                                <th width="8%" class="py-3">Total</th>
                                <th width="3%" class="py-3"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                {{-- Totals Section --}}
                <div class="row justify-content-end mt-4">
                    <div class="col-md-5 col-lg-4">
                        <div class="card bg-gradient-light border-0 rounded-4 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                                    <span class="fw-semibold text-muted">Subtotal:</span>
                                    <span class="fw-bold fs-5">₹ <span id="subTotal">0.00</span></span>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-semibold text-muted mb-1">Extra Charges:</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0">₹</span>
                                        <input type="number" step="0.01" name="extra_charges" id="extraCharges" 
                                               class="form-control border-start-0 ps-0" value="0">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="fw-semibold text-muted mb-1">Round Off:</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0">₹</span>
                                        <input type="number" step="0.01" name="round_off" id="roundOff" 
                                               class="form-control border-start-0 ps-0" value="0">
                                    </div>
                                </div>
                                <hr class="my-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-5 fw-bold">Grand Total:</span>
                                    <span class="fs-3 fw-bold text-gradient">₹ <span id="grandTotal">0.00</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hidden Items Template --}}
                <div style="display: none;">
                    <select id="itemTemplate">
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-end gap-3 mt-5 pt-3 border-top">
                    <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary px-5 py-2 rounded-pill">
                        <i class="fas fa-times me-2"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-gradient-success px-5 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-save me-2"></i> Save Purchase
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let rowIndex = 0;
    let isLoading = false;
    const itemCache = new Map();

    function addRow() {
        const tbody = document.querySelector("#purchaseTable tbody");
        const row = createEmptyRow(rowIndex);
        tbody.insertAdjacentHTML('beforeend', row);
        rowIndex++;
        
        setTimeout(() => {
            const lastRow = tbody.lastElementChild;
            const itemSelect = lastRow.querySelector('.itemSelect');
            if (itemSelect) itemSelect.focus();
        }, 100);
    }

    function createEmptyRow(index) {
        return `
            <tr data-row-index="${index}">
                <td class="text-center row-number fw-bold bg-light">${index + 1}</td>
                <td>
                    <select name="items[${index}][item_id]" class="form-select form-select-sm itemSelect border-0 bg-light" required>
                        <option value="">-- Select Item --</option>
                        ${document.getElementById('itemTemplate').innerHTML}
                    </select>
                    <input type="hidden" name="items[${index}][barcode]" class="barcode">
                    <input type="hidden" name="items[${index}][rack]" class="rack">
                    <input type="hidden" name="items[${index}][hsn_code]" class="hsnCode">
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
                    <input type="date" name="items[${index}][expiry_date]" class="form-control form-control-sm expiryDate" required>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${index}][mrp]" class="form-control form-control-sm mrp text-end" 
                           value="0">
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${index}][purchase_rate]" class="form-control form-control-sm rate text-end" required>
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
                    <button type="button" onclick="removeRow(this)" class="btn btn-danger btn-sm rounded-circle" style="width: 32px; height: 32px; padding: 0;">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    function removeRow(btn) {
        const row = btn.closest('tr');
        row.remove();
        refreshRowNumbers();
        calculateTotal();
    }

    function refreshRowNumbers() {
        document.querySelectorAll('#purchaseTable tbody tr').forEach((tr, i) => {
            const rowNumber = tr.querySelector('.row-number');
            if (rowNumber) rowNumber.innerText = i + 1;
            tr.setAttribute('data-row-index', i);
        });
    }

    async function loadItemData(itemId, row) {
        if (!itemId) return;
        
        if (itemCache.has(itemId)) {
            populateItemData(itemCache.get(itemId), row);
            return;
        }
        
        if (isLoading) return;
        isLoading = true;
        
        try {
            const response = await fetch(`/purchase/get-item/${itemId}`);
            if (!response.ok) throw new Error('Failed to load item data');
            const data = await response.json();
            itemCache.set(itemId, data);
            populateItemData(data, row);
        } catch (error) {
            console.error('Error loading item:', error);
            alert('Failed to load item details. Please try again.');
        } finally {
            isLoading = false;
        }
    }

    function populateItemData(data, row) {
        const gstField = row.querySelector('.gst');
        if (gstField) {
            gstField.value = data.gst_percent || 0;
            calculateRowTotal(row);
        }
        
        if (data.barcode) row.querySelector('.barcode').value = data.barcode;
        if (data.rack) row.querySelector('.rack').value = data.rack;
        if (data.hsn_code) row.querySelector('.hsnCode').value = data.hsn_code;
        
        calculateRowTotal(row);
        calculateTotal();
    }

    function calculateRowTotal(row) {
        const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
        const freeQty = parseFloat(row.querySelector('.freeQty')?.value) || 0;
        const totalQty = qty + freeQty;
        const rate = parseFloat(row.querySelector('.rate')?.value) || 0;
        const gst = parseFloat(row.querySelector('.gst')?.value) || 0;
        let discP = parseFloat(row.querySelector('.discP')?.value) || 0;
        let discA = parseFloat(row.querySelector('.discA')?.value) || 0;
        
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
        document.querySelectorAll('.amount').forEach(el => {
            const val = parseFloat(el.value);
            if (!isNaN(val)) subtotal += val;
        });
        
        const extra = parseFloat(document.getElementById('extraCharges')?.value) || 0;
        const round = parseFloat(document.getElementById('roundOff')?.value) || 0;
        const grand = subtotal + extra + round;
        
        document.getElementById('subTotal').innerText = subtotal.toFixed(2);
        document.getElementById('grandTotal').innerText = grand.toFixed(2);
    }

    // Event Delegation
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('itemSelect')) {
            const itemId = e.target.value;
            const row = e.target.closest('tr');
            
            const selectedItems = [...document.querySelectorAll('.itemSelect')].filter(select => select.value === itemId && select !== e.target);
            if (selectedItems.length > 0) {
                alert('This item is already selected in another row!');
                e.target.value = '';
                return;
            }
            
            if (itemId) {
                e.target.disabled = true;
                loadItemData(itemId, row).finally(() => {
                    e.target.disabled = false;
                });
            } else {
                row.querySelector('.gst').value = '';
                row.querySelector('.barcode').value = '';
                row.querySelector('.rack').value = '';
                row.querySelector('.hsnCode').value = '';
                calculateRowTotal(row);
                calculateTotal();
            }
        }
    });

    document.addEventListener('input', function(e) {
        const row = e.target.closest('tr');
        if (!row) return;
        
        if (e.target.classList.contains('qty') || e.target.classList.contains('freeQty') || 
            e.target.classList.contains('rate') || e.target.classList.contains('gst') || 
            e.target.classList.contains('discP') || e.target.classList.contains('discA')) {
            calculateRowTotal(row);
            calculateTotal();
        }
    });

    document.addEventListener('input', function(e) {
        const row = e.target.closest('tr');
        if (!row) return;
        
        if (e.target.classList.contains('discP')) {
            const discP = parseFloat(e.target.value) || 0;
            if (discP > 100) e.target.value = 100;
            const discAField = row.querySelector('.discA');
            if (discAField && discP > 0) discAField.value = 0;
            calculateRowTotal(row);
            calculateTotal();
        }
        
        if (e.target.classList.contains('discA')) {
            const discPField = row.querySelector('.discP');
            const discA = parseFloat(e.target.value) || 0;
            if (discPField && discA > 0) discPField.value = 0;
            calculateRowTotal(row);
            calculateTotal();
        }
    });

    // Keyboard Navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === "Enter" && !e.target.closest('button')) {
            e.preventDefault();
            const inputs = [...document.querySelectorAll('#purchaseForm input, #purchaseForm select')];
            const currentIndex = inputs.indexOf(document.activeElement);
            if (currentIndex > -1 && currentIndex < inputs.length - 1) {
                inputs[currentIndex + 1].focus();
            }
        }
        
        if (e.key === "Delete" && e.ctrlKey && e.target.closest('tr')) {
            const row = e.target.closest('tr');
            if (row && document.querySelectorAll('#purchaseTable tbody tr').length > 1) {
                const deleteBtn = row.querySelector('.btn-danger');
                if (deleteBtn) removeRow(deleteBtn);
            }
        }
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        addRow();
        
        document.getElementById('extraCharges')?.addEventListener('input', calculateTotal);
        document.getElementById('roundOff')?.addEventListener('input', calculateTotal);
        
        document.getElementById('purchaseForm').addEventListener('submit', function(e) {
            let hasError = false;
            
            document.querySelectorAll('.qty').forEach(qty => {
                const qtyVal = parseFloat(qty.value);
                const freeQtyVal = parseFloat(qty.closest('tr').querySelector('.freeQty')?.value) || 0;
                const totalQty = qtyVal + freeQtyVal;
                if (isNaN(qtyVal) || qtyVal <= 0 || totalQty <= 0) {
                    hasError = true;
                }
            });
            
            document.querySelectorAll('.itemSelect').forEach(select => {
                if (!select.value) {
                    hasError = true;
                }
            });
            
            document.querySelectorAll('.batchNumber').forEach(input => {
                if (!input.value.trim()) {
                    hasError = true;
                }
            });
            
            document.querySelectorAll('.expiryDate').forEach(input => {
                if (!input.value) {
                    hasError = true;
                }
            });
            
            if (hasError) {
                e.preventDefault();
                alert('Please fill all required fields:\n- Select item\n- Enter quantity > 0\n- Enter batch number\n- Select expiry date');
            }
        });
    });
</script>

<style>
    /* Gradients */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }
    .btn-gradient-primary {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }
    .btn-gradient-primary:hover {
        background: linear-gradient(135deg, #163158 0%, #1f3d6e 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        color: white;
    }
    .btn-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }
    .btn-gradient-success:hover {
        background: linear-gradient(135deg, #0d7a6f 0%, #2bc46a 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        color: white;
    }
    .text-gradient {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
    .bg-gradient-light {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    .bg-white-20 {
        background: rgba(255,255,255,0.2);
    }
    
    /* Form Controls */
    .form-control-lg, .form-select-lg {
        font-size: 0.95rem;
        padding: 0.6rem 1rem;
        border-radius: 0.5rem;
    }
    .form-control, .form-select {
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #2a5298;
        box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.15);
    }
    
    /* Table Styles */
    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom-width: 1px;
        background-color: #f8fafc;
    }
    .table td {
        vertical-align: middle;
        padding: 0.5rem;
    }
    input[readonly] {
        background-color: #f8fafc;
        cursor: not-allowed;
    }
    
    /* Card & Table Responsive */
    .card {
        border-radius: 1rem;
        overflow: hidden;
    }
    .card-header {
        border-bottom: none;
    }
    .table-responsive {
        border-radius: 0.75rem;
        overflow-x: auto;
    }
    
    /* Buttons */
    .rounded-pill {
        border-radius: 50px !important;
    }
    .rounded-circle {
        width: 32px !important;
        height: 32px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .btn-outline-secondary {
        border-color: #cbd5e1;
        color: #64748b;
    }
    .btn-outline-secondary:hover {
        background-color: #f1f5f9;
        border-color: #94a3b8;
        color: #475569;
    }
    
    /* Input Group */
    .input-group-text {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem !important;
        }
        .table th, .table td {
            font-size: 0.7rem;
            padding: 0.3rem;
        }
        .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
        }
    }
</style>
@endsection