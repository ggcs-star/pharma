@extends('layouts.master')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="card shadow-xl border-0 rounded-4 overflow-hidden">
        {{-- Enhanced Header with Modern Gradient --}}
        <div class="card-header bg-gradient-primary text-white py-4 border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 opacity-10">
                <i class="fas fa-chart-line fa-6x"></i>
            </div>
            <div class="d-flex justify-content-between align-items-center position-relative flex-wrap gap-3">
                <div>
                    <h4 class="mb-0 fw-bold">
                        <i class="fas fa-shopping-cart me-2"></i> Sales Entry
                    </h4>
                    <p class="text-white-50 small mt-2 mb-0">Create new sales invoice with ease</p>
                </div>
                <div class="text-end">
                    <div class="bg-white-20 rounded-3 px-4 py-2 backdrop-blur-sm">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <span class="small fw-semibold">{{ date('d-m-Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-lg-5">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-exclamation-circle me-3 mt-1 fs-5"></i>
                        <div class="flex-grow-1">
                            <strong class="d-block mb-2">Please fix the following errors:</strong>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('sales.store') }}" id="salesForm">
                @csrf

                {{-- Enhanced Header Information Section --}}
                <div class="row g-4 mb-5">
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input type="text" class="form-control bg-light" id="billNo" value="Auto Generated" readonly>
                            <label for="billNo">
                                <i class="fas fa-receipt text-primary me-1"></i> Bill No
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input type="date" name="bill_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            <label>
                                <i class="fas fa-calendar-day text-primary me-1"></i> Bill Date <span class="text-danger">*</span>
                            </label>
                        </div>
                    </div>
                    
                    {{-- Enhanced Customer Selection with Add Button --}}
                    <div class="col-md-3 col-sm-6">
                        <div class="input-group">
                            <div class="form-floating flex-grow-1">
                                <select name="customer_id" id="customer_id" class="form-select" required>
                                    <option value="">🚶 Walk-in Patient</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} {{ $c->phone ? '(' . $c->phone . ')' : '' }}</option>
                                    @endforeach
                                </select>
                                <label>
                                    <i class="fas fa-user-injured text-primary me-1"></i> Patient <span class="text-danger">*</span>
                                </label>
                            </div>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal" title="Add New Patient">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                        </div>
                        <small class="text-muted ms-1">Select existing or add new patient</small>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <select name="doctor_id" class="form-select">
                                <option value="">👨‍⚕️ Select Doctor</option>
                                @foreach($doctors as $doc)
                                    <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->clinic_city }})</option>
                                @endforeach
                            </select>
                            <label>
                                <i class="fas fa-user-md text-primary me-1"></i> Doctor
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Enhanced Payment Type Section --}}
                <div class="row mb-5">
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <select name="payment_type" class="form-select" required>
                                <option value="cash">💵 Cash</option>
                                <option value="card">💳 Card</option>
                                <option value="upi">📱 UPI</option>
                                <option value="credit">🏦 Credit</option>
                            </select>
                            <label>
                                <i class="fas fa-credit-card text-primary me-1"></i> Payment Mode <span class="text-danger">*</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Add Item Button with Icon Animation --}}
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h5 class="mb-0 text-dark fw-semibold">
                            <i class="fas fa-boxes text-primary me-2"></i> Sales Items
                        </h5>
                        <p class="text-muted small mt-1 mb-0">Add products with batch details</p>
                    </div>
                    <button type="button" class="btn btn-gradient-primary rounded-pill px-4 py-2 btn-animate" onclick="addRow()">
                        <i class="fas fa-plus-circle me-2"></i> Add Item
                    </button>
                </div>

                {{-- Enhanced Items Table with Better Structure --}}
                <div class="table-responsive mb-4 rounded-3 border shadow-sm">
                    <table class="table table-hover align-middle mb-0" id="salesTable">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th width="3%" class="py-3">#</th>
                                <th width="15%" class="py-3">Product <span class="text-danger">*</span></th>
                                <th width="8%" class="py-3">Pack</th>
                                <th width="8%" class="py-3">Batch No</th>
                                <th width="8%" class="py-3">Expiry</th>
                                <th width="8%" class="py-3">Sale Type</th>
                                <th width="6%" class="py-3">Qty <span class="text-danger">*</span></th>
                                <th width="7%" class="py-3">Price (₹)</th>
                                <th width="7%" class="py-3">MRP (₹)</th>
                                <th width="5%" class="py-3">GST%</th>
                                <th width="6%" class="py-3">Disc%</th>
                                <th width="7%" class="py-3">Taxable</th>
                                <th width="7%" class="py-3">GST</th>
                                <th width="8%" class="py-3">Total (₹)</th>
                                <th width="3%" class="py-3"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                {{-- Enhanced Totals Section with Card Design --}}
                <div class="row justify-content-end mt-4">
                    <div class="col-md-5 col-lg-4">
                        <div class="card bg-gradient-light border-0 rounded-4 shadow-sm overflow-hidden">
                            <div class="card-header bg-transparent border-0 pt-4 px-4">
                                <h6 class="text-muted mb-0 text-uppercase small fw-semibold">Summary</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                                    <span class="fw-semibold text-muted">Subtotal:</span>
                                    <span class="fw-bold fs-5 text-dark">₹ <span id="subTotal">0.00</span></span>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-semibold text-muted mb-2 small">Discount (₹):</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">₹</span>
                                        <input type="number" step="0.01" name="discount_amount" id="discountAmount" 
                                               class="form-control border-start-0 ps-0" value="0" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="fw-semibold text-muted mb-2 small">Round Off:</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">₹</span>
                                        <input type="number" step="0.01" name="round_off" id="roundOff" 
                                               class="form-control border-start-0 ps-0" value="0" placeholder="0.00">
                                    </div>
                                </div>
                                <hr class="my-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-5 fw-bold text-dark">Grand Total:</span>
                                    <span class="fs-2 fw-bold text-gradient">₹ <span id="grandTotal">0.00</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Enhanced Action Buttons --}}
                <div class="d-flex justify-content-end gap-3 mt-5 pt-3 border-top">
                    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary px-5 py-2 rounded-pill btn-animate">
                        <i class="fas fa-times me-2"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-gradient-success px-5 py-2 rounded-pill shadow-sm btn-animate">
                        <i class="fas fa-print me-2"></i> Submit & Print
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Enhanced Add Customer Modal --}}
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-gradient-primary text-white border-0">
                <h5 class="modal-title fw-bold" id="addCustomerModalLabel">
                    <i class="fas fa-user-plus me-2"></i> Add New Patient
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modalAlert"></div>
                <form id="addCustomerForm">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-user text-primary me-1"></i> Patient Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="customer_name" class="form-control form-control-lg rounded-3" 
                               placeholder="Enter patient name" autocomplete="off">
                        <div class="invalid-feedback">Please enter patient name</div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-phone-alt text-primary me-1"></i> Mobile Number
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">+91</span>
                            <input type="tel" name="mobile" id="customer_mobile" class="form-control form-control-lg" 
                                   placeholder="Enter mobile number" autocomplete="off">
                        </div>
                        <small class="text-muted">Optional, but recommended for future visits</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-map-marker-alt text-primary me-1"></i> Address
                        </label>
                        <textarea name="address" id="customer_address" class="form-control rounded-3" rows="3" 
                                  placeholder="Enter full address"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancel
                </button>
                <button type="button" class="btn btn-gradient-primary rounded-pill px-4" id="saveCustomerBtn">
                    <i class="fas fa-save me-2"></i> Save Patient
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let rowIndex = 0;

// AUTO ADD FIRST ROW
window.onload = () => {
    addRow();
};

// ADD ROW
function addRow() {
    let items = `@foreach($items as $item)
        <option value="{{ $item->id }}">{{ $item->name }}</option>
    @endforeach`;

    let row = `
    <table>
        <td class="text-center row-number fw-bold bg-light">${rowIndex + 1}</td>
        <td>
            <select name="items[${rowIndex}][item_id]" class="form-select form-select-sm itemSelect" required>
                <option value="">-- Select Item --</option>
                ${items}
            </select>
        </td>
        <td>
            <span class="pack badge bg-light text-dark w-100 text-start px-2 py-2"></span>
        </td>
        <td>
            <input type="hidden" name="items[${rowIndex}][batch_id]" class="batch_id">
            <input type="text" class="form-control batch" readonly>
        </td>
        <td><input type="date" class="form-control expiry" readonly></td>
        <td>
            <select name="items[${rowIndex}][sale_type]" class="form-select saleType">
                <option value="strip">Strip</option>
                <option value="loose">Loose</option>
            </select>
        </td>
        <td>
            <input type="number" name="items[${rowIndex}][qty]" class="form-control qty" value="1" step="any">
        </td>
        <td>
            <input type="number" name="items[${rowIndex}][selling_price]" class="form-control price" step="any">
        </td>
        <td>
            <input type="number" class="form-control mrp" readonly>
        </td>
        <td>
            <input type="number" class="form-control gst" readonly>
        </td>
        <td>
            <input type="number" class="form-control discount" value="0" step="any">
        </td>
        <td>
            <input type="number" class="form-control taxable bg-light" readonly>
        </td>
        <td>
            <input type="number" class="form-control gstAmt bg-light" readonly>
        </td>
        <td>
            <input type="number" class="form-control total bg-light" readonly>
        </td>
        <td class="text-center">
            <button type="button" onclick="removeRow(this)" class="btn btn-danger btn-sm rounded-circle">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    </tr>
    `;

    document.querySelector('#salesTable tbody').insertAdjacentHTML('beforeend', row);
    rowIndex++;
}

// REMOVE ROW
function removeRow(btn) {
    let row = btn.closest('tr');
    row.remove();
    calculateGrandTotal();
    reindexRows();
}

// Reindex row numbers after deletion
function reindexRows() {
    let rows = document.querySelectorAll('#salesTable tbody tr');
    rows.forEach((row, idx) => {
        row.cells[0].innerText = idx + 1;
        // Update all input names
        let selects = row.querySelectorAll('select[name^="items["]');
        selects.forEach(select => {
            let name = select.getAttribute('name');
            let newName = name.replace(/items\[\d+\]/, `items[${idx}]`);
            select.setAttribute('name', newName);
        });
        
        let inputs = row.querySelectorAll('input[name^="items["]');
        inputs.forEach(input => {
            let name = input.getAttribute('name');
            if (name) {
                let newName = name.replace(/items\[\d+\]/, `items[${idx}]`);
                input.setAttribute('name', newName);
            }
        });
    });
    rowIndex = rows.length;
}

// Helper function to trigger calculation
function triggerCalculation(row) {
    let qty = parseFloat(row.querySelector('.qty').value) || 0;
    let price = parseFloat(row.querySelector('.price').value) || 0;
    let disc = parseFloat(row.querySelector('.discount').value) || 0;
    let gst = parseFloat(row.querySelector('.gst').value) || 0;

    let amount = qty * price;
    let discountAmount = (amount * disc) / 100;
    let taxable = amount - discountAmount;
    let gstAmount = (taxable * gst) / 100;
    let final = taxable + gstAmount;

    row.querySelector('.total').value = final.toFixed(2);
    row.querySelector('.taxable').value = taxable.toFixed(2);
    row.querySelector('.gstAmt').value = gstAmount.toFixed(2);
    calculateGrandTotal();
}

// CALCULATION on input events
document.addEventListener('input', function(e) {
    let row = e.target.closest('tr');
    if (!row) return;

    let targetClass = e.target.classList;
    if (targetClass.contains('qty') || targetClass.contains('price') || 
        targetClass.contains('discount')) {
        
        let qty = parseFloat(row.querySelector('.qty').value) || 0;
        let price = parseFloat(row.querySelector('.price').value) || 0;
        let disc = parseFloat(row.querySelector('.discount').value) || 0;
        let gst = parseFloat(row.querySelector('.gst').value) || 0;

        let amount = qty * price;
        let discountAmount = (amount * disc) / 100;
        let taxable = amount - discountAmount;
        let gstAmount = (taxable * gst) / 100;
        let final = taxable + gstAmount;

        row.querySelector('.total').value = final.toFixed(2);
        row.querySelector('.taxable').value = taxable.toFixed(2);
        row.querySelector('.gstAmt').value = gstAmount.toFixed(2);
        calculateGrandTotal();
    }
});

// GRAND TOTAL with extra discount and round off
function calculateGrandTotal() {
    let subtotal = 0;
    document.querySelectorAll('.total').forEach(el => {
        let val = parseFloat(el.value);
        if (!isNaN(val)) {
            subtotal += val;
        }
    });
    
    let discount = parseFloat(document.getElementById('discountAmount')?.value) || 0;
    let roundOff = parseFloat(document.getElementById('roundOff')?.value) || 0;
    let grandTotal = subtotal - discount + roundOff;
    
    document.getElementById('subTotal').innerText = subtotal.toFixed(2);
    document.getElementById('grandTotal').innerText = grandTotal.toFixed(2);
}
// ITEM SELECT - Fetch item details and batch
document.addEventListener('change', function(e) {

    if (e.target.classList.contains('itemSelect')) {

        let row = e.target.closest('tr');
        let id = e.target.value;

        if (!id) return;

        /*
        |--------------------------------------------------------------------------
        | BASIC ITEM DATA
        |--------------------------------------------------------------------------
        */

        fetch(`/api/item-details/${id}`)
            .then(res => res.json())
            .then(data => {

                let packing = (data.pack_type || '').toLowerCase();
                let tablets = 0;

                // match: "strip of 10"
                let match1 = packing.match(/strip\s*of\s*(\d+)/i);

                // match: "10 tablets"
                let match2 = packing.match(/(\d+)\s*tablet/i);

                // match: "10"
                let match3 = packing.match(/(\d+)/);

                if (match1) {

                    tablets = parseInt(match1[1]);

                } else if (match2) {

                    tablets = parseInt(match2[1]);

                } else if (match3) {

                    tablets = parseInt(match3[1]);
                }

                /*
                |--------------------------------------------------------------------------
                | PACKING DISPLAY
                |--------------------------------------------------------------------------
                */

                let packingText = tablets > 0
                    ? `1 Strip = ${tablets} Tablets`
                    : '—';

                row.querySelector('.pack').innerText = packingText;

                /*
                |--------------------------------------------------------------------------
                | GST & CONVERSION FACTOR
                |--------------------------------------------------------------------------
                */

                row.querySelector('.gst').value = data.gst ?? 0;

                row.dataset.conversionFactor =
                    data.conversion_factor ?? 1;

            })
            .catch(err => console.error('Error fetching item details:', err));

        /*
        |--------------------------------------------------------------------------
        | BATCH DATA
        |--------------------------------------------------------------------------
        */

        fetch(`/api/get-item-full/${id}`)
            .then(res => res.json())
            .then(data => {

                /*
                |--------------------------------------------------------------------------
                | BATCH DETAILS
                |--------------------------------------------------------------------------
                */

                row.querySelector('.batch').value =
                    data.batch ?? '';

                row.querySelector('.batch_id').value =
                    data.batch_id ?? '';

                /*
                |--------------------------------------------------------------------------
                | EXPIRY DATE
                |--------------------------------------------------------------------------
                */

                if (data.expiry) {

                    let d = new Date(data.expiry);

                    row.querySelector('.expiry').value =
                        d.getFullYear() + '-' +
                        String(d.getMonth() + 1).padStart(2, '0') + '-' +
                        String(d.getDate()).padStart(2, '0');
                }

                /*
                |--------------------------------------------------------------------------
                | PRICE LOGIC
                |--------------------------------------------------------------------------
                | UI me MRP column rahega
                | But backend se OFFLINE PRICE use hoga
                |--------------------------------------------------------------------------
                */

                let offlinePrice = parseFloat(data.offline_price ?? 0);

                /*
                |--------------------------------------------------------------------------
                | PRICE INPUT
                |--------------------------------------------------------------------------
                */

                row.querySelector('.price').value =
                    offlinePrice.toFixed(2);

                /*
                |--------------------------------------------------------------------------
                | MRP COLUMN
                |--------------------------------------------------------------------------
                | MRP field me bhi offline price show karo
                |--------------------------------------------------------------------------
                */

                row.querySelector('.mrp').value =
                    offlinePrice.toFixed(2);

                /*
                |--------------------------------------------------------------------------
                | SAVE FOR LOOSE / STRIP CALCULATION
                |--------------------------------------------------------------------------
                */

                row.dataset.offlinePrice = offlinePrice;

                /*
                |--------------------------------------------------------------------------
                | SALE TYPE LOGIC
                |--------------------------------------------------------------------------
                */

                let saleTypeSelect =
                    row.querySelector('.saleType');

                if (
                    (data.loose_stock ?? 0) > 0 &&
                    (data.stock ?? 0) <= 0
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | ONLY LOOSE AVAILABLE
                    |--------------------------------------------------------------------------
                    */

                    saleTypeSelect.innerHTML = `
                        <option value="loose" selected>
                            Loose Tablet
                        </option>
                    `;

                    saleTypeSelect.value = 'loose';

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | STRIP AVAILABLE
                    |--------------------------------------------------------------------------
                    */

                    saleTypeSelect.innerHTML = `
                        <option value="strip" selected>
                            Full Strip
                        </option>

                        <option value="loose">
                            Loose Tablet
                        </option>
                    `;

                    saleTypeSelect.value = 'strip';
                }

                /*
                |--------------------------------------------------------------------------
                | INITIAL CALCULATION
                |--------------------------------------------------------------------------
                */

                triggerCalculation(row);

            })
            .catch(err =>
                console.error(
                    'Error fetching batch details:',
                    err
                )
            );
    }

    /*
    |--------------------------------------------------------------------------
    | SALE TYPE CHANGE
    |--------------------------------------------------------------------------
    */

    if (e.target.classList.contains('saleType')) {

        let row = e.target.closest('tr');

        let saleType = e.target.value;

        let qtyInput =
            row.querySelector('.qty');

        let priceInput =
            row.querySelector('.price');

        /*
        |--------------------------------------------------------------------------
        | OFFLINE PRICE
        |--------------------------------------------------------------------------
        */

        let stripPrice =
            parseFloat(row.dataset.offlinePrice || 0);

        /*
        |--------------------------------------------------------------------------
        | CONVERSION FACTOR
        |--------------------------------------------------------------------------
        */

        let conversionFactor =
            parseFloat(
                row.dataset.conversionFactor || 1
            );

        /*
        |--------------------------------------------------------------------------
        | STRIP SALE
        |--------------------------------------------------------------------------
        */

        if (saleType === 'strip') {

            qtyInput.placeholder = 'Strip Qty';

            priceInput.value =
                stripPrice.toFixed(2);

        }

        /*
        |--------------------------------------------------------------------------
        | LOOSE TABLET SALE
        |--------------------------------------------------------------------------
        */

        else {

            qtyInput.placeholder = 'Tablet Qty';

            let perTabletPrice =
                stripPrice / conversionFactor;

            priceInput.value =
                perTabletPrice.toFixed(2);
        }

        /*
        |--------------------------------------------------------------------------
        | RECALCULATE
        |--------------------------------------------------------------------------
        */

        triggerCalculation(row);
    }
});

// ==================== CUSTOMER AJAX FUNCTIONALITY ====================
document.addEventListener('DOMContentLoaded', function() {
    // Add Customer AJAX
    const saveBtn = document.getElementById('saveCustomerBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', async function() {
            const nameInput = document.getElementById('customer_name');
            const name = nameInput.value.trim();
            
            // Validation
            if (!name) {
                nameInput.classList.add('is-invalid');
                showModalAlert('Please enter patient name', 'danger');
                return;
            }
            nameInput.classList.remove('is-invalid');
            
            const mobile = document.getElementById('customer_mobile').value.trim();
            const address = document.getElementById('customer_address').value.trim();
            
            // Show loading state
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving...';
            saveBtn.disabled = true;
            
            try {
                const response = await fetch('{{ route("customers.ajax-store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        name: name,
                        mobile: mobile,
                        address: address
                    })
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    // Add new customer to dropdown
                    const customerSelect = document.getElementById('customer_id');
                    const newOption = document.createElement('option');
                    newOption.value = data.customer.id;
                    newOption.textContent = data.customer.phone 
                        ? `${data.customer.name} (${data.customer.phone})`
                        : data.customer.name;
                    customerSelect.appendChild(newOption);
                    
                    // Auto-select the new customer
                    customerSelect.value = data.customer.id;
                    
                    // Show success message
                    showModalAlert(data.message, 'success');
                    
                    // Close modal after short delay
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addCustomerModal'));
                        if (modal) modal.hide();
                        resetModalForm();
                    }, 1500);
                } else {
                    showModalAlert(data.message || 'Failed to save patient', 'danger');
                }
            } catch (error) {
                console.error('Error:', error);
                showModalAlert('Network error. Please try again.', 'danger');
            } finally {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            }
        });
    }
    
    // Reset modal when hidden
    const modal = document.getElementById('addCustomerModal');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            resetModalForm();
        });
    }
    
    // Clear validation on input
    document.getElementById('customer_name')?.addEventListener('input', function() {
        this.classList.remove('is-invalid');
        clearModalAlert();
    });
});

function showModalAlert(message, type) {
    const alertDiv = document.getElementById('modalAlert');
    if (alertDiv) {
        alertDiv.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show rounded-3" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        setTimeout(() => {
            const alert = alertDiv.querySelector('.alert');
            if (alert) alert.classList.remove('show');
            setTimeout(() => { alertDiv.innerHTML = ''; }, 300);
        }, 3000);
    }
}

function clearModalAlert() {
    const alertDiv = document.getElementById('modalAlert');
    if (alertDiv) alertDiv.innerHTML = '';
}

function resetModalForm() {
    const form = document.getElementById('addCustomerForm');
    if (form) form.reset();
    const nameInput = document.getElementById('customer_name');
    if (nameInput) nameInput.classList.remove('is-invalid');
    clearModalAlert();
}

// Extra charges and round off listeners
document.getElementById('discountAmount')?.addEventListener('input', calculateGrandTotal);
document.getElementById('roundOff')?.addEventListener('input', calculateGrandTotal);

// Keyboard shortcut for adding item (Ctrl+Enter)
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'Enter') {
        e.preventDefault();
        addRow();
    }
});
</script>

<style>
    /* Enhanced Gradients */
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
    .form-control, .form-select {
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #2a5298;
        box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.15);
    }
    
    /* Form Floating */
    .form-floating > label {
        font-weight: 500;
        color: #6c757d;
    }
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        color: #2a5298;
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
    
    /* Buttons Animation */
    .btn-animate {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-animate:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .btn-animate:active {
        transform: translateY(0);
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
    
    /* Modal Styles */
    .modal-content {
        border-radius: 1rem;
    }
    .modal-header {
        border-bottom: none;
    }
    .modal-footer {
        border-top: none;
    }
    
    /* Badge Styles */
    .badge {
        font-weight: 500;
        font-size: 0.8rem;
    }
    
    /* Backdrop Blur */
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem !important;
        }
        .table th, .table td {
            font-size: 0.65rem;
            padding: 0.25rem;
        }
        .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
        }
        .form-floating {
            font-size: 0.85rem;
        }
    }

    @media (max-width: 576px) {
        .table th, .table td {
            font-size: 0.55rem;
            padding: 0.2rem;
        }
        .btn-sm {
            padding: 0.2rem 0.4rem;
        }
        .rounded-circle {
            width: 28px !important;
            height: 28px !important;
        }
    }
</style>
@endsection