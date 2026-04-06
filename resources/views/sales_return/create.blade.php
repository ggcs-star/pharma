@extends('layouts.master')

@section('content')

<div class="container-fluid px-4">
    <!-- HEADER SECTION with better styling -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-1" style="color: #2c3e50;">
                <i class="fas fa-exchange-alt me-2 text-danger"></i>Sales Return
            </h4>
            <p class="text-muted small mb-0">Process product returns and manage refunds</p>
        </div>
        <a href="{{ route('sales.return.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <!-- BILL SEARCH CARD - Enhanced Design -->
    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
        <div class="card-body p-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <label class="form-label fw-semibold mb-2">
                        <i class="fas fa-receipt me-1 text-primary"></i> Bill Number
                    </label>
                    <form method="GET" action="{{ route('sales.return.create') }}" class="d-flex gap-2">
                        <input type="text" name="bill_number"
                               class="form-control form-control-lg rounded-3 border-2"
                               placeholder="Enter Bill Number (e.g., SAL-0001)"
                               value="{{ request('bill_number') }}"
                               style="background: #fff; border-color: #e0e0e0;">
                        <button class="btn btn-primary px-4 rounded-3 btn-lg">
                            <i class="fas fa-search me-1"></i> Fetch Bill
                        </button>
                    </form>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="bg-light rounded-3 p-2 d-inline-block">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i> Enter valid bill number to continue
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($sale)
    <form method="POST" action="{{ route('sales_return.store') }}" id="returnForm">
        @csrf

        <input type="hidden" name="sale_id" value="{{ $sale->id }}">
        <input type="hidden" name="return_number" value="SR-{{ time() }}">

        <!-- SALE INFO SUMMARY CARD -->
        <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-white fw-semibold">
                        <i class="fas fa-shopping-cart me-2"></i> Sale Information
                    </h6>
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                        <i class="fas fa-hashtag me-1"></i> Bill: {{ $sale->bill_number }}
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle p-2 me-2">
                                <i class="fas fa-calendar-alt text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Sale Date</small>
                                <span class="fw-semibold">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle p-2 me-2">
                                <i class="fas fa-user text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Customer</small>
                                <span class="fw-semibold">{{ $sale->customer->name ?? 'Walk-in Customer' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle p-2 me-2">
                                <i class="fas fa-rupee-sign text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Bill Amount</small>
                                <span class="fw-semibold">₹ {{ number_format($sale->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle p-2 me-2">
                                <i class="fas fa-exchange-alt text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Return Status</small>
                                <span class="badge bg-info text-dark rounded-pill">In Progress</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ITEMS TABLE CARD -->
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header py-3" style="background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);">
                <h6 class="mb-0 text-white fw-semibold">
                    <i class="fas fa-boxes me-2"></i> Return Items
                </h6>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-center">
                            <th class="py-3">#</th>
                            <th class="py-3 text-start">Item</th>
                            <th class="py-3">Batch</th>
                            <th class="py-3">Sold Qty</th>
                            <th class="py-3">Returned</th>
                            <th class="py-3">Returnable</th>
                            <th class="py-3" width="130">Return Qty</th>
                            <th class="py-3">Rate (₹)</th>
                            <th class="py-3">Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $i => $item)
                            @php
                                $returned = $item->returned_qty ?? 0;
                                $returnable = $item->quantity - $returned;
                                $itemName = $item->item->name ?? 'Unknown';
                                $batchCode = $item->batch->batch_code ?? '-';
                            @endphp
                            <tr class="text-center" data-item-id="{{ $item->id }}">
                                <td class="fw-semibold text-muted">{{ $i + 1 }}</td>
                                <td class="text-start">
                                    <div class="fw-semibold">{{ $itemName }}</div>
                                    <small class="text-muted">ID: {{ $item->item_id }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-dark px-3 py-2 rounded-pill">
                                        <i class="fas fa-cube me-1"></i>{{ $batchCode }}
                                    </span>
                                </td>
                                <td class="fw-semibold">{{ $item->quantity }}</td>
                                <td class="text-warning fw-semibold">{{ $returned }}</td>
                                <td class="text-success fw-bold returnable fs-5">{{ $returnable }}</td>
                                <td>
                                    <input type="number"
                                           class="form-control return_qty text-center rounded-3 border-2"
                                           name="items[{{ $i }}][qty_return]"
                                           value="0"
                                           min="0"
                                           max="{{ $returnable }}"
                                           step="1"
                                           style="width: 110px; margin: 0 auto;"
                                           data-max="{{ $returnable }}"
                                           data-rate="{{ $item->selling_price }}">
                                    <div class="invalid-feedback" style="font-size: 10px;"></div>
                                </td>
                                <td class="rate fw-semibold">₹ {{ number_format($item->selling_price, 2) }}</td>
                                <td>
                                    <input type="text"
                                           class="form-control amount text-center bg-light fw-semibold border-0"
                                           readonly
                                           style="background: #f8f9fa; width: 100px; margin: 0 auto;">
                                </td>
                                <input type="hidden" name="items[{{ $i }}][sale_item_id]" value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $i }}][item_id]" value="{{ $item->item_id }}">
                                <input type="hidden" name="items[{{ $i }}][batch_id]" value="{{ $item->batch_id }}">
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- FOOTER WITH TOTAL AND ACTION -->
            <div class="card-footer bg-white py-4 border-top">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 rounded-3" style="background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);">
                                <small class="text-muted d-block">Total Return Amount</small>
                                <h3 class="mb-0 fw-bold" style="color: #dc3545;">
                                    ₹ <span id="total_text">0.00</span>
                                </h3>
                            </div>
                            <div class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>
                                Return amount will be refunded to customer
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <input type="hidden" id="total_return" name="total_amount">
                        <button type="submit" class="btn btn-danger btn-lg px-5 rounded-pill shadow-sm" id="submitBtn">
                            <i class="fas fa-check-circle me-2"></i> Process Return
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @elseif(request('bill_number'))
    <!-- No sale found message -->
    <div class="alert alert-warning border-0 rounded-4 shadow-sm">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
            <div>
                <h5 class="mb-1">No sale found</h5>
                <p class="mb-0">Bill number "{{ request('bill_number') }}" does not exist. Please check and try again.</p>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .table th {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
    }
    .table td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }
    .return_qty:focus {
        border-color: #ff4b2b;
        box-shadow: 0 0 0 0.2rem rgba(255, 75, 43, 0.25);
    }
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
    }
    .btn-danger {
        background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
        border: none;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 75, 43, 0.3);
    }
    .rounded-4 {
        border-radius: 1rem !important;
    }
    .rounded-3 {
        border-radius: 0.75rem !important;
    }
    .returnable {
        font-size: 1.1rem;
        font-weight: 700;
    }
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        opacity: 0.5;
    }
    input[type=number]:hover::-webkit-inner-spin-button,
    input[type=number]:hover::-webkit-outer-spin-button {
        opacity: 1;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('returnForm');
    const submitBtn = document.getElementById('submitBtn');

    function updateRow(row) {
        const qtyInput = row.querySelector('.return_qty');
        let qty = parseFloat(qtyInput.value) || 0;
        const max = parseFloat(qtyInput.getAttribute('data-max')) || 0;
        const rate = parseFloat(qtyInput.getAttribute('data-rate')) || 0;
        
        // Validation
        if (qty < 0) {
            qtyInput.value = 0;
            qty = 0;
        }
        
        if (qty > max) {
            qtyInput.value = max;
            qty = max;
            showToast('Return quantity cannot exceed returnable quantity', 'warning');
        }
        
        // Calculate amount
        const amount = qty * rate;
        const amountField = row.querySelector('.amount');
        amountField.value = amount.toFixed(2);
        
        // Highlight row if quantity > 0
        if (qty > 0) {
            row.style.backgroundColor = '#fff8e7';
        } else {
            row.style.backgroundColor = '';
        }
        
        calculateTotal();
    }
    
    function calculateTotal() {
        let total = 0;
        let hasReturn = false;
        
        document.querySelectorAll('.amount').forEach(el => {
            const val = parseFloat(el.value) || 0;
            total += val;
            if (val > 0) hasReturn = true;
        });
        
        document.getElementById('total_text').innerText = total.toFixed(2);
        document.getElementById('total_return').value = total.toFixed(2);
        
        // Enable/disable submit button
        if (submitBtn) {
            if (total <= 0) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.6';
                submitBtn.style.cursor = 'not-allowed';
            } else {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
            }
        }
        
        return total;
    }
    
    // Add event listeners to all quantity inputs
    document.querySelectorAll('.return_qty').forEach(input => {
        input.addEventListener('input', function() {
            const row = this.closest('tr');
            updateRow(row);
        });
        
        // Add blur validation
        input.addEventListener('blur', function() {
            let val = parseFloat(this.value) || 0;
            const max = parseFloat(this.getAttribute('data-max')) || 0;
            if (val > max) {
                this.value = max;
                const row = this.closest('tr');
                updateRow(row);
            }
            if (val < 0) {
                this.value = 0;
                const row = this.closest('tr');
                updateRow(row);
            }
        });
    });
    
    // Form submission validation
    if (form) {
        form.addEventListener('submit', function(e) {
            const total = calculateTotal();
            if (total <= 0) {
                e.preventDefault();
                showToast('Please select at least one item to return', 'error');
                return false;
            }
            
            // Confirm before submission
            if (!confirm(`Are you sure you want to process this return?\nTotal Return Amount: ₹${total.toFixed(2)}`)) {
                e.preventDefault();
                return false;
            }
            
            // Disable button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
        });
    }
    
    // Toast notification function
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `position-fixed bottom-0 end-0 m-3 alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show shadow-lg rounded-3`;
        toast.style.zIndex = '9999';
        toast.style.minWidth = '300px';
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                <span>${message}</span>
                <button type="button" class="btn-close ms-3" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
    
    // Initialize totals and check submit button state
    calculateTotal();
    
    // Add keyboard shortcut (Ctrl + Enter to submit)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            if (submitBtn && !submitBtn.disabled) {
                form.dispatchEvent(new Event('submit'));
            }
        }
    });
    
    // Animate returnable quantity badges
    document.querySelectorAll('.returnable').forEach(el => {
        if (parseInt(el.innerText) === 0) {
            el.style.color = '#dc3545';
        }
    });
});
</script>

@if(session('success'))
<script>
    showToast('{{ session('success') }}', 'success');
</script>
@endif

@if(session('error'))
<script>
    showToast('{{ session('error') }}', 'error');
</script>
@endif

@endsection