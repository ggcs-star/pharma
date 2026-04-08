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

    /* Alert Styles */
    .alert-info-custom {
        background: #eff6ff;
        border-left: 4px solid #0ea5e9;
        padding: 16px 20px;
        border-radius: 16px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .alert-info-custom i {
        color: #0ea5e9;
        font-size: 1.2rem;
    }

    .alert-info-custom .alert-content {
        flex: 1;
        color: #1e40af;
        font-size: 0.85rem;
    }

    .alert-info-custom .alert-content strong {
        font-weight: 700;
    }

    .badge-item-info {
        background: #dbeafe;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #1e40af;
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
        gap: 24px;
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

    .form-control-custom:disabled, 
    .form-control-custom[readonly] {
        background: #f8fafc;
        color: #64748b;
        cursor: not-allowed;
        border-color: #e2e8f0;
    }

    /* Read-only field styling */
    .readonly-field {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: #475569;
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
        margin-top: 16px;
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

    .price-preview-value.margin-positive {
        color: #10b981;
    }

    .price-preview-value.margin-negative {
        color: #ef4444;
    }

    .price-preview-value.margin-low {
        color: #eab308;
    }

    /* Form Actions */
    .form-actions {
        padding: 24px 32px;
        background: #fafcff;
        display: flex;
        justify-content: flex-end;
        gap: 16px;
    }

    .btn-update {
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

    .btn-update:hover {
        transform: translateY(-2px);
        background: linear-gradient(135deg, #0284c7, #2563eb);
        box-shadow: 0 6px 14px rgba(14, 165, 233, 0.3);
    }

    .btn-cancel {
        background: #f1f5f9;
        border: none;
        padding: 12px 24px;
        border-radius: 14px;
        font-weight: 600;
        font-size: 0.85rem;
        color: #475569;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-cancel:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* Error Styles */
    .error-message {
        color: #ef4444;
        font-size: 0.7rem;
        margin-top: 4px;
    }

    .form-control-custom.is-invalid {
        border-color: #ef4444;
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

        .form-actions {
            flex-direction: column-reverse;
            padding: 20px;
        }

        .btn-update, .btn-cancel {
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
            <i class="fa fa-edit"></i>
            Edit Catalog
        </h3>
        <p>Update product catalog details, pricing, and batch information</p>
    </div>
    <a href="{{ route('supplier.catalogs.index') }}" class="btn-back">
        <i class="fa fa-arrow-left"></i> Back to Catalog List
    </a>
</div>

<!-- Item Information Alert -->
<div class="alert-info-custom">
    <i class="fa fa-info-circle"></i>
    <div class="alert-content">
        <strong>Editing Catalog for:</strong> 
        <span class="badge-item-info">
            <i class="fa fa-capsules"></i> {{ $catalog->item->name ?? 'N/A' }}
        </span>
        @if($catalog->item && $catalog->item->generic_name)
            <span style="margin-left: 8px;">({{ $catalog->item->generic_name }})</span>
        @endif
    </div>
</div>

<div class="form-container">
    <form method="POST" action="{{ route('supplier.catalogs.update', $catalog->id) }}" id="catalogForm">
        @csrf
        @method('PUT')

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
                           value="{{ old('batch_no', $catalog->batch_no) }}"
                           placeholder="e.g., BATCH-2024-001" 
                           class="form-control-custom @error('batch_no') is-invalid @enderror"
                           required>
                    <div class="helper-text">Unique batch identifier for tracking</div>
                    @error('batch_no')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-calendar"></i>
                        Expiry Date <span class="required">*</span>
                    </label>
                    <input type="date" 
                           name="expiry_date" 
                           value="{{ old('expiry_date', $catalog->expiry_date) }}"
                           class="form-control-custom @error('expiry_date') is-invalid @enderror"
                           min="{{ date('Y-m-d') }}"
                           required>
                    <div class="helper-text">Must be a future date</div>
                    @error('expiry_date')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-cubes"></i>
                        Current Stock
                    </label>
                    <input type="number" 
                           value="{{ $catalog->real_stock ?? $catalog->qty ?? 0 }}"
                           class="form-control-custom readonly-field"
                           readonly
                           disabled>
                    <div class="helper-text">Stock quantity (read-only, update via stock module)</div>
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
                           value="{{ old('purchase_price', $catalog->purchase_price) }}"
                           placeholder="Enter purchase price" 
                           class="form-control-custom @error('purchase_price') is-invalid @enderror"
                           min="0"
                           required>
                    <div class="helper-text">Your cost price per unit</div>
                    @error('purchase_price')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-tag"></i>
                        Retailer Price <span class="required">*</span>
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="retailer_price" 
                           value="{{ old('retailer_price', $catalog->retailer_price) }}"
                           placeholder="Enter selling price" 
                           class="form-control-custom @error('retailer_price') is-invalid @enderror"
                           min="0"
                           required>
                    <div class="helper-text">Your selling price to retailers</div>
                    @error('retailer_price')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-money-bill"></i>
                        MRP (Maximum Retail Price)
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="retailer_mrp" 
                           value="{{ old('retailer_mrp', $catalog->retailer_mrp) }}"
                           placeholder="Enter MRP" 
                           class="form-control-custom @error('retailer_mrp') is-invalid @enderror"
                           min="0">
                    <div class="helper-text">Printed maximum retail price</div>
                    @error('retailer_mrp')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fa fa-percent"></i>
                        GST Percentage
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="gst_percent" 
                           value="{{ old('gst_percent', $catalog->gst_percent) }}"
                           placeholder="Enter GST %" 
                           class="form-control-custom @error('gst_percent') is-invalid @enderror"
                           min="0"
                           max="28">
                    <div class="helper-text">Standard GST rate (0%, 5%, 12%, 18%, 28%)</div>
                    @error('gst_percent')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Price Preview Section -->
            <div class="price-preview" id="pricePreview">
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
                        <div class="price-preview-value" id="previewMargin">0%</div>
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
            <a href="{{ route('supplier.catalogs.index') }}" class="btn-cancel">
                <i class="fa fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn-update">
                <i class="fa fa-save"></i> Update Catalog
            </button>
        </div>
    </form>
</div>

<script>
    // Get current values
    const purchaseInput = document.querySelector('input[name="purchase_price"]');
    const retailerInput = document.querySelector('input[name="retailer_price"]');
    const mrpInput = document.querySelector('input[name="retailer_mrp"]');

    function updatePreview() {
        const purchase = parseFloat(purchaseInput?.value) || 0;
        const retailer = parseFloat(retailerInput?.value) || 0;
        const mrp = parseFloat(mrpInput?.value) || 0;

        // Update price displays
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
            if (discount < 0) {
                discountElement.style.color = '#ef4444';
            } else {
                discountElement.style.color = '#10b981';
            }
        } else if (mrp > 0 && retailer === 0) {
            discountElement.innerHTML = '100%';
            discountElement.style.color = '#10b981';
        } else {
            discountElement.innerHTML = '0%';
            discountElement.style.color = '#64748b';
        }
    }

    // Add event listeners
    if (purchaseInput) purchaseInput.addEventListener('input', updatePreview);
    if (retailerInput) retailerInput.addEventListener('input', updatePreview);
    if (mrpInput) mrpInput.addEventListener('input', updatePreview);

    // Initial preview update
    updatePreview();

    // GST quick select helper
    const gstInput = document.querySelector('input[name="gst_percent"]');
    if (gstInput) {
        gstInput.addEventListener('focus', function() {
            this.placeholder = "Common rates: 0, 5, 12, 18, 28";
        });
    }

    // Warn before leaving if changes are made
    let formChanged = false;
    const form = document.getElementById('catalogForm');
    const formInputs = form.querySelectorAll('input, select, textarea');
    
    formInputs.forEach(input => {
        input.addEventListener('change', () => { formChanged = true; });
        input.addEventListener('input', () => { formChanged = true; });
    });
    
    window.addEventListener('beforeunload', (e) => {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            return e.returnValue;
        }
    });
    
    form.addEventListener('submit', () => { formChanged = false; });
</script>

@endsection