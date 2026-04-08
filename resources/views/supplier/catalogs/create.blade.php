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

    select.form-control-custom {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
    }

    /* Price Row (inline fields) */
    .price-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    /* Helper Text */
    .helper-text {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 4px;
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

    /* Alert/Error Styles */
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

        .price-row {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .form-actions {
            flex-direction: column-reverse;
            padding: 20px;
        }

        .btn-save, .btn-reset {
            justify-content: center;
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
                    <select name="item_id" class="form-control-custom @error('item_id') is-invalid @enderror" required>
                        <option value="">-- Choose an item --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }} @if($item->generic_name) ({{ $item->generic_name }}) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('item_id')
                        <div class="helper-text" style="color: #ef4444;">{{ $message }}</div>
                    @enderror
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
                    <div class="helper-text">Unique batch identifier for tracking</div>
                    @error('batch_no')
                        <div class="helper-text" style="color: #ef4444;">{{ $message }}</div>
                    @enderror
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
                    <div class="helper-text">Must be a future date</div>
                    @error('expiry_date')
                        <div class="helper-text" style="color: #ef4444;">{{ $message }}</div>
                    @enderror
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
                    <div class="helper-text">Number of units available</div>
                    @error('qty')
                        <div class="helper-text" style="color: #ef4444;">{{ $message }}</div>
                    @enderror
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
                    <div class="helper-text">Your cost price per unit</div>
                    @error('purchase_price')
                        <div class="helper-text" style="color: #ef4444;">{{ $message }}</div>
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
                           value="{{ old('retailer_price') }}"
                           placeholder="Enter selling price" 
                           class="form-control-custom @error('retailer_price') is-invalid @enderror"
                           min="0"
                           required>
                    <div class="helper-text">Your selling price to retailers</div>
                    @error('retailer_price')
                        <div class="helper-text" style="color: #ef4444;">{{ $message }}</div>
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
                           value="{{ old('retailer_mrp') }}"
                           placeholder="Enter MRP" 
                           class="form-control-custom @error('retailer_mrp') is-invalid @enderror"
                           min="0">
                    <div class="helper-text">Printed maximum retail price</div>
                    @error('retailer_mrp')
                        <div class="helper-text" style="color: #ef4444;">{{ $message }}</div>
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
                           value="{{ old('gst_percent') }}"
                           placeholder="Enter GST %" 
                           class="form-control-custom @error('gst_percent') is-invalid @enderror"
                           min="0"
                           max="28">
                    <div class="helper-text">Standard GST rate (0%, 5%, 12%, 18%, 28%)</div>
                    @error('gst_percent')
                        <div class="helper-text" style="color: #ef4444;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Preview Section (Optional) -->
        <div class="form-section" id="previewSection" style="display: none;">
            <div class="section-title">
                <i class="fa fa-eye"></i>
                Price Preview
            </div>
            <div class="price-preview" style="background: #f8fafc; border-radius: 16px; padding: 20px;">
                <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <span style="font-size: 0.7rem; color: #64748b;">Purchase Price</span>
                        <div><strong id="previewPurchase">₹0.00</strong></div>
                    </div>
                    <div>
                        <span style="font-size: 0.7rem; color: #64748b;">Retailer Price</span>
                        <div><strong id="previewRetailer">₹0.00</strong></div>
                    </div>
                    <div>
                        <span style="font-size: 0.7rem; color: #64748b;">MRP</span>
                        <div><strong id="previewMrp">₹0.00</strong></div>
                    </div>
                    <div>
                        <span style="font-size: 0.7rem; color: #64748b;">Margin</span>
                        <div><strong id="previewMargin" style="color: #10b981;">0%</strong></div>
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
    // Auto-calculate margin preview
    const purchaseInput = document.querySelector('input[name="purchase_price"]');
    const retailerInput = document.querySelector('input[name="retailer_price"]');
    const mrpInput = document.querySelector('input[name="retailer_mrp"]');
    const previewSection = document.getElementById('previewSection');

    function updatePreview() {
        const purchase = parseFloat(purchaseInput?.value) || 0;
        const retailer = parseFloat(retailerInput?.value) || 0;
        const mrp = parseFloat(mrpInput?.value) || 0;

        document.getElementById('previewPurchase').innerHTML = `₹${purchase.toFixed(2)}`;
        document.getElementById('previewRetailer').innerHTML = `₹${retailer.toFixed(2)}`;
        document.getElementById('previewMrp').innerHTML = `₹${mrp.toFixed(2)}`;

        // Calculate margin percentage
        if (purchase > 0 && retailer > 0) {
            const margin = ((retailer - purchase) / purchase) * 100;
            const marginElement = document.getElementById('previewMargin');
            marginElement.innerHTML = `${margin.toFixed(1)}%`;
            if (margin < 0) {
                marginElement.style.color = '#ef4444';
            } else if (margin < 10) {
                marginElement.style.color = '#eab308';
            } else {
                marginElement.style.color = '#10b981';
            }
        } else {
            document.getElementById('previewMargin').innerHTML = '0%';
        }

        // Show preview if any price is entered
        if (purchase > 0 || retailer > 0 || mrp > 0) {
            previewSection.style.display = 'block';
        } else {
            previewSection.style.display = 'none';
        }
    }

    // Add event listeners
    if (purchaseInput) purchaseInput.addEventListener('input', updatePreview);
    if (retailerInput) retailerInput.addEventListener('input', updatePreview);
    if (mrpInput) mrpInput.addEventListener('input', updatePreview);

    // Reset function
    function resetForm() {
        document.getElementById('catalogForm').reset();
        setTimeout(updatePreview, 100);
    }

    // Initial preview update
    updatePreview();

    // GST quick select helper
    const gstInput = document.querySelector('input[name="gst_percent"]');
    if (gstInput) {
        gstInput.addEventListener('focus', function() {
            this.placeholder = "Common rates: 0, 5, 12, 18, 28";
        });
    }
</script>

@endsection