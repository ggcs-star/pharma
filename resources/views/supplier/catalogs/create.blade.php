@extends('supplier.layouts.app')

@section('content')
<style>
    /* Form Page Styles */
    .form-header {
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

    .form-title h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 6px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .form-title h3 i {
        color: #0ea5e9;
        font-size: 1.6rem;
    }

    .form-title p {
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

    /* Form Container */
    .form-container {
        background: white;
        border-radius: 24px;
        border: 1px solid #eef2ff;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }

    .form-section {
        padding: 28px 32px;
        border-bottom: 1px solid #f1f5f9;
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .section-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 2px solid #eef2ff;
    }

    .section-title i {
        color: #0ea5e9;
        font-size: 1.1rem;
    }

    /* Form Grid */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: span 2;
    }

    .form-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-label i {
        color: #0ea5e9;
        font-size: 0.8rem;
        width: 16px;
    }

    .form-label .required {
        color: #ef4444;
        font-size: 0.7rem;
    }

    .form-control-custom {
        padding: 12px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        background: #fafcff;
        color: #0f172a;
    }

    .form-control-custom:focus {
        outline: none;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        background: white;
    }

    .form-control-custom[readonly] {
        background: #f8fafc;
        color: #475569;
        border-color: #e2e8f0;
        cursor: default;
    }

    /* Error state styles */
    .form-control-custom.is-invalid {
        border-color: #ef4444;
        background-color: #fef2f2;
    }

    .form-control-custom.is-invalid:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .invalid-feedback-custom {
        font-size: 0.7rem;
        color: #ef4444;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .invalid-feedback-custom i {
        font-size: 0.65rem;
    }

    select.form-control-custom {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
    }

    /* Info Row for Pack Details */
    .info-row {
        background: #f8fafc;
        border-radius: 16px;
        padding: 16px 20px;
        margin-top: 16px;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .info-item {
        text-align: center;
    }

    .info-item-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 6px;
    }

    .info-item-value {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0f172a;
    }

    /* Helper Text */
    .helper-text {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 4px;
    }

    /* Price Preview */
    .price-preview {
        background: #f8fafc;
        border-radius: 16px;
        padding: 20px;
    }

    .price-preview-grid {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }

    .price-preview-item {
        text-align: center;
        flex: 1;
        min-width: 100px;
    }

    .price-preview-label {
        font-size: 0.7rem;
        color: #64748b;
        display: block;
        margin-bottom: 6px;
    }

    .price-preview-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
    }

    /* Form Actions */
    .form-actions {
        padding: 24px 32px;
        background: #fafcff;
        display: flex;
        justify-content: flex-end;
        gap: 16px;
    }

    .btn-save {
        background: linear-gradient(135deg, #0ea5e9, #3b82f6);
        border: none;
        padding: 12px 32px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 0.85rem;
        color: white;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        background: linear-gradient(135deg, #0284c7, #2563eb);
        box-shadow: 0 6px 14px rgba(14, 165, 233, 0.3);
    }

    .btn-reset {
        background: #f1f5f9;
        border: none;
        padding: 12px 24px;
        border-radius: 14px;
        font-weight: 600;
        font-size: 0.85rem;
        color: #475569;
        transition: all 0.2s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-reset:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* Alert Styles */
    .alert-danger-custom {
        background: #fef2f2;
        border-left: 4px solid #ef4444;
        padding: 16px 20px;
        border-radius: 16px;
        margin-bottom: 24px;
    }

    .alert-danger-custom ul {
        margin: 0;
        padding-left: 20px;
        color: #b91c1c;
        font-size: 0.85rem;
    }

    .alert-success-custom {
        background: #ecfdf5;
        border-left: 4px solid #10b981;
        padding: 16px 20px;
        border-radius: 16px;
        margin-bottom: 24px;
        color: #065f46;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
        }

        .form-section {
            padding: 20px;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .form-group.full-width {
            grid-column: span 1;
        }

        .info-row {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .form-actions {
            flex-direction: column-reverse;
            padding: 20px;
        }

        .btn-save, .btn-reset {
            justify-content: center;
        }

        .price-preview-grid {
            flex-direction: column;
            gap: 12px;
        }

        .price-preview-item {
            text-align: left;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .price-preview-label {
            margin-bottom: 0;
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

    .form-container {
        animation: fadeIn 0.3s ease;
    }
</style>

<div class="form-header">
    <div class="form-title">
        <h3>
            <i class="fa fa-plus-circle"></i>
            Add New Catalog
        </h3>
        <p>Create a new product catalog with batch details and pricing</p>
    </div>
    <a href="{{ route('supplier.catalogs.index') }}" class="btn-back">
        <i class="fa fa-arrow-left"></i> Back to Catalog List
    </a>
</div>

<!-- Display Validation Errors -->
@if($errors->any())
<div class="alert-danger-custom">
    <strong><i class="fa fa-exclamation-triangle"></i> Please fix the following errors:</strong>
    <ul class="mt-2 mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Display Success Message -->
@if(session('success'))
<div class="alert-success-custom">
    <i class="fa fa-check-circle fa-lg"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="form-container">
    <form method="POST" action="{{ route('supplier.catalogs.store') }}" id="catalogForm">
        @csrf

        <!-- Product Information Section -->
        <div class="form-section">
            <div class="section-title">
                <i class="fa fa-box"></i>
                Product Information
            </div>

            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">
                        <i class="fa fa-capsules"></i>
                        Select Item <span class="required">*</span>
                    </label>
                    <select name="item_id" id="itemSelect" class="form-control-custom @error('item_id') is-invalid @enderror" required>
                        <option value="">-- Choose an item --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" 
                                    data-pack="{{ $item->packType->name ?? '' }}"
                                    data-unit="{{ $item->unit->name ?? '' }}"
                                    data-units="{{ $item->number_of_units ?? '' }}"
                                    {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('item_id')
                        <div class="invalid-feedback-custom">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                    <div class="helper-text">Select the product from the list</div>
                </div>
            </div>

            <!-- Pack Details Info Row -->
            <div class="info-row" id="packInfoRow" style="display: none;">
                <div class="info-item">
                    <div class="info-item-label">
                        <i class="fa fa-box"></i> Pack Type
                    </div>
                    <div class="info-item-value" id="packType">-</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">
                        <i class="fa fa-balance-scale"></i> Unit
                    </div>
                    <div class="info-item-value" id="unit">-</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">
                        <i class="fa fa-calculator"></i> No. of Units
                    </div>
                    <div class="info-item-value" id="numberOfUnits">-</div>
                </div>
            </div>
        </div>

        <!-- Batch & Stock Section -->
        <div class="form-section">
            <div class="section-title">
                <i class="fa fa-barcode"></i>
                Batch & Stock Details
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-tag"></i>
                        Batch Number <span class="required">*</span>
                    </label>
                    <input type="text" 
                           name="batch_no" 
                           value="{{ old('batch_no') }}"
                           placeholder="e.g., BATCH-2024-001" 
                           class="form-control-custom @error('batch_no') is-invalid @enderror"
                           required>
                    @error('batch_no')
                        <div class="invalid-feedback-custom">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                    <div class="helper-text">Unique batch identifier for tracking</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-calendar"></i>
                        Expiry Date <span class="required">*</span>
                    </label>
                    <input type="date" 
                           name="expiry_date" 
                           value="{{ old('expiry_date') }}"
                           class="form-control-custom @error('expiry_date') is-invalid @enderror"
                           min="{{ date('Y-m-d') }}"
                           required>
                    @error('expiry_date')
                        <div class="invalid-feedback-custom">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                    <div class="helper-text">Must be a future date</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-cubes"></i>
                        Initial Stock Quantity <span class="required">*</span>
                    </label>
                    <input type="number" 
                           name="qty" 
                           value="{{ old('qty') }}"
                           placeholder="Enter quantity" 
                           class="form-control-custom @error('qty') is-invalid @enderror"
                           min="0"
                           step="1"
                           required>
                    @error('qty')
                        <div class="invalid-feedback-custom">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                    <div class="helper-text" id="unitHelpText">Select an item to see unit details</div>
                </div>
            </div>
        </div>

        <!-- Pricing Section -->
        <div class="form-section">
            <div class="section-title">
                <i class="fa fa-rupee-sign"></i>
                Pricing Information
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-chart-line"></i>
                        Purchase Price <span class="required">*</span>
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="purchase_price" 
                           value="{{ old('purchase_price') }}"
                           placeholder="Enter purchase price" 
                           class="form-control-custom @error('purchase_price') is-invalid @enderror"
                           min="0"
                           required>
                    @error('purchase_price')
                        <div class="invalid-feedback-custom">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                    <div class="helper-text">Your cost price per unit</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-tag"></i>
                        Retailer Price <span class="required">*</span>
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="retailer_price" 
                           value="{{ old('retailer_price') }}"
                           placeholder="Enter selling price" 
                           class="form-control-custom @error('retailer_price') is-invalid @enderror"
                           min="0"
                           required>
                    @error('retailer_price')
                        <div class="invalid-feedback-custom">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                    <div class="helper-text">Your selling price to retailers</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-tag"></i>
                        Base Price
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="base_price" 
                           value="{{ old('base_price') }}"
                           placeholder="Enter base price" 
                           class="form-control-custom @error('base_price') is-invalid @enderror"
                           min="0">
                    @error('base_price')
                        <div class="invalid-feedback-custom">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                    <div class="helper-text">Optional: Base reference price</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-money-bill"></i>
                        MRP (Maximum Retail Price)
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="retailer_mrp" 
                           value="{{ old('retailer_mrp') }}"
                           placeholder="Enter MRP" 
                           class="form-control-custom @error('retailer_mrp') is-invalid @enderror"
                           min="0">
                    @error('retailer_mrp')
                        <div class="invalid-feedback-custom">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                    <div class="helper-text">Printed maximum retail price</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-percent"></i>
                        GST Percentage
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="gst_percent" 
                           value="{{ old('gst_percent') }}"
                           placeholder="Enter GST %" 
                           class="form-control-custom @error('gst_percent') is-invalid @enderror"
                           min="0"
                           max="28">
                    @error('gst_percent')
                        <div class="invalid-feedback-custom">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                    <div class="helper-text">Standard GST rate (0%, 5%, 12%, 18%, 28%)</div>
                </div>
            </div>

            <!-- Price Preview Section -->
            <div class="price-preview" id="pricePreview" style="margin-top: 20px; display: none;">
                <div class="price-preview-grid">
                    <div class="price-preview-item">
                        <span class="price-preview-label">Purchase Price</span>
                        <div class="price-preview-value" id="previewPurchase">₹0.00</div>
                    </div>
                    <div class="price-preview-item">
                        <span class="price-preview-label">Retailer Price</span>
                        <div class="price-preview-value" id="previewRetailer">₹0.00</div>
                    </div>
                    <div class="price-preview-item">
                        <span class="price-preview-label">MRP</span>
                        <div class="price-preview-value" id="previewMrp">₹0.00</div>
                    </div>
                    <div class="price-preview-item">
                        <span class="price-preview-label">Margin</span>
                        <div class="price-preview-value" id="previewMargin" style="color: #10b981;">0%</div>
                    </div>
                    <div class="price-preview-item">
                        <span class="price-preview-label">Discount to MRP</span>
                        <div class="price-preview-value" id="previewDiscount">0%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="reset" class="btn-reset" onclick="resetForm()">
                <i class="fa fa-undo-alt"></i> Reset
            </button>
            <button type="submit" class="btn-save">
                <i class="fa fa-save"></i> Save Catalog
            </button>
        </div>
    </form>
</div>

<script>
    // Get DOM elements
    const itemSelect = document.getElementById('itemSelect');
    const packTypeEl = document.getElementById('packType');
    const unitEl = document.getElementById('unit');
    const numberOfUnitsEl = document.getElementById('numberOfUnits');
    const packInfoRow = document.getElementById('packInfoRow');
    const unitHelpText = document.getElementById('unitHelpText');
    
    const purchaseInput = document.querySelector('input[name="purchase_price"]');
    const retailerInput = document.querySelector('input[name="retailer_price"]');
    const mrpInput = document.querySelector('input[name="retailer_mrp"]');
    const pricePreview = document.getElementById('pricePreview');

    // Update pack info when item is selected
    function updatePackInfo() {
        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const pack = selectedOption.dataset.pack || '';
            const unit = selectedOption.dataset.unit || '';
            const units = selectedOption.dataset.units || '';
            
            packTypeEl.textContent = pack || '-';
            unitEl.textContent = unit || '-';
            numberOfUnitsEl.textContent = units || '-';
            packInfoRow.style.display = 'grid';
            
            // Update helper text
            if (pack && unit && units) {
                unitHelpText.innerHTML = `<i class="fa fa-info-circle"></i> 1 ${pack} = ${units} ${unit}`;
            } else {
                unitHelpText.innerHTML = 'Unit details not available for this item';
            }
        } else {
            packInfoRow.style.display = 'none';
            unitHelpText.innerHTML = 'Select an item to see unit details';
        }
    }

    // Update price preview
    function updatePricePreview() {
        const purchase = parseFloat(purchaseInput?.value) || 0;
        const retailer = parseFloat(retailerInput?.value) || 0;
        const mrp = parseFloat(mrpInput?.value) || 0;

        document.getElementById('previewPurchase').innerHTML = `₹${purchase.toFixed(2)}`;
        document.getElementById('previewRetailer').innerHTML = `₹${retailer.toFixed(2)}`;
        document.getElementById('previewMrp').innerHTML = `₹${mrp.toFixed(2)}`;

        // Calculate margin percentage
        const marginElement = document.getElementById('previewMargin');
        if (purchase > 0 && retailer > 0) {
            const margin = ((retailer - purchase) / purchase) * 100;
            marginElement.innerHTML = `${margin.toFixed(1)}%`;
            if (margin < 0) {
                marginElement.style.color = '#ef4444';
            } else if (margin < 10) {
                marginElement.style.color = '#eab308';
            } else {
                marginElement.style.color = '#10b981';
            }
        } else {
            marginElement.innerHTML = '0%';
            marginElement.style.color = '#64748b';
        }

        // Calculate discount to MRP
        const discountElement = document.getElementById('previewDiscount');
        if (mrp > 0 && retailer > 0) {
            const discount = ((mrp - retailer) / mrp) * 100;
            discountElement.innerHTML = `${discount.toFixed(1)}%`;
            discountElement.style.color = discount < 0 ? '#ef4444' : '#10b981';
        } else if (mrp > 0 && retailer === 0) {
            discountElement.innerHTML = '100%';
            discountElement.style.color = '#10b981';
        } else {
            discountElement.innerHTML = '0%';
            discountElement.style.color = '#64748b';
        }

        // Show/hide price preview
        if (purchase > 0 || retailer > 0 || mrp > 0) {
            pricePreview.style.display = 'block';
        } else {
            pricePreview.style.display = 'none';
        }
    }

    // Reset form function
    function resetForm() {
        document.getElementById('catalogForm').reset();
        setTimeout(() => {
            updatePackInfo();
            updatePricePreview();
        }, 100);
        
        // Clear validation error styles
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
    }

    // Event listeners
    if (itemSelect) {
        itemSelect.addEventListener('change', updatePackInfo);
    }
    
    if (purchaseInput) purchaseInput.addEventListener('input', updatePricePreview);
    if (retailerInput) retailerInput.addEventListener('input', updatePricePreview);
    if (mrpInput) mrpInput.addEventListener('input', updatePricePreview);

    // GST quick select helper
    const gstInput = document.querySelector('input[name="gst_percent"]');
    if (gstInput) {
        gstInput.addEventListener('focus', function() {
            this.placeholder = "Common rates: 0, 5, 12, 18, 28";
        });
    }

    // Remove is-invalid class on input focus
    document.querySelectorAll('.form-control-custom').forEach(input => {
        input.addEventListener('focus', function() {
            this.classList.remove('is-invalid');
        });
    });

    // Initial updates
    updatePackInfo();
    updatePricePreview();

    // If there's an old value for item_id, trigger the update
    @if(old('item_id'))
        setTimeout(() => {
            if (itemSelect) {
                itemSelect.value = "{{ old('item_id') }}";
                updatePackInfo();
            }
        }, 100);
    @endif
</script>

@endsection