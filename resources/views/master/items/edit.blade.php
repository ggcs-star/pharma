@extends('layouts.master')

@section('title', 'Edit Product')

@section('content')

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold text-dark">Edit Product</h1>
            <p class="text-muted mt-1">Update product information, media, and inventory</p>
        </div>
        <div>
            <a href="{{route('master.items.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Back to Products
            </a>
            <button type="submit" form="editProductForm" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-check-lg"></i> Update Product
            </button>
        </div>
    </div>

    <form action="{{ route('master.items.update', $item->id) }}" method="POST" enctype="multipart/form-data" id="editProductForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- LEFT COLUMN -->
            <div class="col-lg-8">
                
                <!-- ============================================ -->
                <!-- 1. BASIC INFORMATION CARD -->
                <!-- ============================================ -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle-fill text-primary fs-4 me-2"></i>
                            <div>
                                <h5 class="card-title fw-bold mb-0">Basic Information</h5>
                                <p class="text-muted small mb-0">Essential product details and categorization</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-tag text-muted"></i>
                                    </span>
                                    <input type="text" name="name" value="{{ old('name', $item->name) }}" 
                                           class="form-control border-start-0 ps-0" placeholder="Enter product name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Manufacturer <span class="text-danger">*</span></label>
                                <select name="manufacturer_id" class="form-select" required>
                                    <option value="">Select Manufacturer</option>
                                    @foreach($manufacturers as $manufacturer)
                                        <option value="{{ $manufacturer->id }}"
                                            {{ old('manufacturer_id', $item->manufacturer_id) == $manufacturer->id ? 'selected' : '' }}>
                                            {{ $manufacturer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Brand</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-building text-muted"></i>
                                    </span>
                                    <input type="text" name="brand" value="{{ old('brand', $item->brand) }}" 
                                           class="form-control border-start-0 ps-0" placeholder="Enter brand name">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Sub Category</label>
                                <select name="sub_category_id" class="form-select">
                                    <option value="">Select Sub Category</option>
                                    @foreach($subCategories as $sub)
                                        <option value="{{ $sub->id }}"
                                            {{ old('sub_category_id', $item->sub_category_id) == $sub->id ? 'selected' : '' }}>
                                            {{ $sub->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" class="form-control" rows="4" 
                                          placeholder="Enter detailed product description...">{{ old('description', $item->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- 2. PRODUCT MEDIA CARD -->
                <!-- ============================================ -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-image-fill text-primary fs-4 me-2"></i>
                            <div>
                                <h5 class="card-title fw-bold mb-0">Product Media</h5>
                                <p class="text-muted small mb-0">Manage main image and product gallery</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Main Image -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-2">Main Image</label>
                            <div class="main-image-wrapper">
                                <input type="file" name="main_image" id="mainImageInput" class="d-none" accept="image/*">
                                
                                <div id="mainImagePreview" class="text-center">
                                    @if($item->main_image)
                                        <div class="position-relative d-inline-block">
                                            <div class="main-image-container border rounded-3 p-3 bg-light" style="width: 100%; max-width: 300px;">
                                            <img id="mainImageImg" 
     src="{{ filter_var($item->main_image, FILTER_VALIDATE_URL) 
        ? $item->main_image 
        : Storage::disk('s3')->url($item->main_image) }}"
     class="img-fluid"
     style="max-height: 250px; width: 100%; object-fit: contain;">
                                            </div>
                                            <button type="button" id="removeMainImageBtn" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                        <div class="mt-3">
                                            <button type="button" id="changeMainImageBtn" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-camera"></i> Change Image
                                            </button>
                                        </div>
                                        <input type="hidden" name="existing_main_image" value="{{ $item->main_image }}">
                                    @else
                                        <div id="mainImagePlaceholder" class="border rounded-3 p-5 bg-light cursor-pointer text-center" style="cursor: pointer; max-width: 300px; margin: 0 auto;">
                                            <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                                            <p class="mt-2 mb-0 text-muted">Click to upload main image</p>
                                            <small class="text-muted">JPG, PNG, WEBP up to 5MB</small>
                                        </div>
                                        <div id="mainImagePreviewExisting" style="display: none;">
                                            <div class="position-relative d-inline-block">
                                                <img id="mainImageImgNew" src="" alt="Main Image" class="img-fluid rounded border" style="height: 200px; object-fit: contain;">
                                                <button type="button" id="removeNewMainImageBtn" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-2">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Gallery Images -->
                        <div>
                            <label class="form-label fw-semibold mb-2">Gallery Images</label>
                            <div class="mb-3">
<input type="file" name="gallery_images[]" id="galleryImagesInput" class="form-control" multiple>                                <small class="text-muted">You can select multiple images for product gallery</small>
                            </div>

                            <!-- Existing Gallery Images -->
                            @if($item->galleryImages && $item->galleryImages->count() > 0)
                                <div class="mb-4">
                                    <label class="form-label fw-semibold small text-muted">Current Gallery Images</label>
                                    <div class="row g-3" id="existingGalleryGrid">
                                        @foreach($item->galleryImages as $galleryImage)
                                            <div class="col-md-3 existing-gallery-item" data-id="{{ $galleryImage->id }}" data-path="{{ $galleryImage->image_path }}">
                                                <div class="position-relative">
                                                    <img src="{{ Storage::disk('s3')->url($galleryImage->image_path) }}" 
                                                         class="img-fluid rounded-3 border shadow-sm" 
                                                         style="height: 120px; width: 100%; object-fit: cover;">
                                                    <button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-2 remove-existing-gallery" style="width: 28px; height: 28px; padding: 0; line-height: 1;">
                                                        <i class="bi bi-x-lg small"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- New Gallery Images Preview -->
                            <div id="newGalleryContainer" style="display: none;">
                                <label class="form-label fw-semibold small text-muted">New Gallery Images</label>
                                <div class="row g-3" id="newGalleryGrid"></div>
                            </div>

                            <div id="deletedGalleryIds"></div>
                        </div>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- 3. PRODUCT DETAILS CARD -->
                <!-- ============================================ -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-star-fill text-primary fs-4 me-2"></i>
                            <div>
                                <h5 class="card-title fw-bold mb-0">Product Details</h5>
                                <p class="text-muted small mb-0">Key features and selling points</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Product Highlights -->
                        <div>
                            <label class="form-label fw-semibold mb-2">Product Highlights</label>
                            <div id="highlightsWrapper">
                                @php
                                    $highlights = is_array($item->product_highlights) 
                                        ? $item->product_highlights 
                                        : (json_decode($item->product_highlights, true) ?? []);
                                @endphp
                                @if(count($highlights) > 0)
                                    @foreach($highlights as $highlight)
                                        <div class="highlight-item mb-2">
                                            <div class="input-group">
                                                <span class="input-group-text bg-transparent border-end-0">
                                                    <i class="bi bi-dot text-primary fs-4"></i>
                                                </span>
                                                <input type="text" name="product_highlights[]" 
                                                       class="form-control border-start-0" 
                                                       value="{{ $highlight }}"
                                                       placeholder="e.g., 100% genuine medicine">
                                                <button type="button" class="btn btn-outline-danger remove-highlight">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="highlight-item mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-end-0">
                                                <i class="bi bi-dot text-primary fs-4"></i>
                                            </span>
                                            <input type="text" name="product_highlights[]" 
                                                   class="form-control border-start-0" 
                                                   placeholder="e.g., 100% genuine medicine">
                                            <button type="button" class="btn btn-outline-danger remove-highlight" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" id="addHighlightBtn" class="btn btn-outline-primary btn-sm mt-2">
                                <i class="bi bi-plus-lg me-1"></i> Add Highlight
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- 4. ERP CONFIGURATION CARD -->
                <!-- ============================================ -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-gear-fill text-primary fs-4 me-2"></i>
                            <div>
                                <h5 class="card-title fw-bold mb-0">ERP Configuration</h5>
                                <p class="text-muted small mb-0">Inventory, pricing, and logistics settings</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Pack Type</label>
                                <select name="pack_id" class="form-select">
                                    <option value="">Select Pack</option>
                                    @foreach($packTypes as $pack)
                                        <option value="{{ $pack->id }}"
                                            {{ old('pack_id', $item->pack_id) == $pack->id ? 'selected' : '' }}>
                                            {{ $pack->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Unit</label>
                                <select name="unit_id" class="form-select">
                                    <option value="">Select Unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}"
                                            {{ old('unit_id', $item->unit_id) == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Number of Units</label>
                                <input type="number" name="number_of_units" value="{{ old('number_of_units', $item->number_of_units) }}" 
                                       class="form-control" placeholder="Quantity per pack">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">GST %</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="gst_percent" 
                                           value="{{ old('gst_percent', $item->gst_percent) }}" 
                                           class="form-control" placeholder="18">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Schedule Type</label>
                                <select name="sch_type" class="form-select">
                                    <option value="">Select</option>
                                    <option value="OTC" {{ old('sch_type', $item->sch_type) == 'OTC' ? 'selected' : '' }}>OTC</option>
                                    <option value="H" {{ old('sch_type', $item->sch_type) == 'H' ? 'selected' : '' }}>Schedule H</option>
                                    <option value="H1" {{ old('sch_type', $item->sch_type) == 'H1' ? 'selected' : '' }}>Schedule H1</option>
                                    <option value="X" {{ old('sch_type', $item->sch_type) == 'X' ? 'selected' : '' }}>Schedule X</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">HSN Code</label>
                                <input type="text" name="hsn_code" value="{{ old('hsn_code', $item->hsn_code) }}" 
                                       class="form-control" placeholder="HSN/SAC code">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Rack Location</label>
                                <input type="text" name="rack" value="{{ old('rack', $item->rack) }}" 
                                       class="form-control" placeholder="Aisle-Shelf-Bin">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Molecule</label>
                                <input type="text" name="molecule" value="{{ old('molecule', $item->molecule) }}" 
                                       class="form-control" placeholder="Active ingredient">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Barcode</label>
                                <input type="text" name="barcode" value="{{ old('barcode', $item->barcode) }}" 
                                       class="form-control" placeholder="Scan code">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Max Discount %</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="max_discount" 
                                           value="{{ old('max_discount', $item->max_discount) }}" 
                                           class="form-control" placeholder="Max discount">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Min Threshold</label>
                                <input type="number" name="min_threshold" value="{{ old('min_threshold', $item->min_threshold) }}" 
                                       class="form-control" placeholder="Low stock alert">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Max Threshold</label>
                                <input type="number" name="max_threshold" value="{{ old('max_threshold', $item->max_threshold) }}" 
                                       class="form-control" placeholder="Maximum stock">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Conversion Factor</label>
                                <input type="number" step="0.01" name="conversion_factor" 
                                       value="{{ old('conversion_factor', $item->conversion_factor ?? 1) }}" 
                                       class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Unit Ratio</label>
                                <input type="number" step="0.01" name="unit_ratio" 
                                       value="{{ old('unit_ratio', $item->unit_ratio ?? 1) }}" 
                                       class="form-control">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea name="notes" class="form-control" rows="3" 
                                          placeholder="Additional notes...">{{ old('notes', $item->notes) }}</textarea>
                            </div>
                            <div class="col-md-12 mt-3">
    <label class="form-label fw-semibold">Available Packings</label>

    <div class="d-flex flex-wrap gap-2">
        @foreach($item->packings as $packing)
            <span class="badge bg-info text-dark px-3 py-2">
                {{ ucfirst($packing->packaging_detail) }}
            </span>
        @endforeach
    </div>
</div>
                        </div>
                    </div>
                </div>
            </div>

            
            <!-- RIGHT COLUMN -->
            <div class="col-lg-4">
                
                <!-- ============================================ -->
                <!-- 5. BATCH MANAGEMENT CARD -->
                <!-- ============================================ -->
                <!-- <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-box-seam-fill text-primary fs-4 me-2"></i>
                            <div>
                                <h5 class="card-title fw-bold mb-0">Batch Management</h5>
                                <p class="text-muted small mb-0">Manage inventory batches</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-3" id="batchTable">
                                <thead class="table-light">
                                    <tr class="text-nowrap">
                                        <th>Batch No</th>
                                        <th>Stock</th>
                                        <th>Expiry</th>
                                        <th>MRP</th>
                                        <th>PTR</th>
                                        <th>Discount</th>
                                        <th>Margin</th>
                                        <th>Markup</th>
                                        <th>Selling Price</th>
                                        <th style="width: 40px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->batches as $index => $batch)
                                    <tr>
                                        <td><input type="text" name="batches[{{ $index }}][batch_code]" value="{{ $batch->batch_code }}" class="form-control form-control-sm"></td>
                                        <td><input type="number" name="batches[{{ $index }}][stock]" value="{{ $batch->stock }}" class="form-control form-control-sm"></td>
                                        <td><input type="date" name="batches[{{ $index }}][expiry_date]" value="{{ $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('Y-m-d') : '' }}" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="0.01" name="batches[{{ $index }}][mrp]" value="{{ $batch->mrp }}" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="0.01" name="batches[{{ $index }}][ptr]" value="{{ $batch->ptr }}" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="0.01" name="batches[{{ $index }}][discount]" value="{{ $batch->discount }}" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="0.01" name="batches[{{ $index }}][margin]" value="{{ $batch->margin }}" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="0.01" name="batches[{{ $index }}][markup]" value="{{ $batch->markup }}" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="0.01" name="batches[{{ $index }}][selling_price]" value="{{ $batch->selling_price }}" class="form-control form-control-sm"></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger removeRow"><i class="bi bi-trash"></i></button></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline-primary w-100" id="addBatch">
                            <i class="bi bi-plus-lg me-1"></i> Add Batch
                        </button>
                    </div>
                </div> -->

                <!-- Status Toggles Card -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-toggle2-on text-primary fs-4 me-2"></i>
                            <div>
                                <h5 class="card-title fw-bold mb-0">Product Status</h5>
                                <p class="text-muted small mb-0">Manage product visibility and restrictions</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="inactive" id="inactiveSwitch" value="1" {{ $item->inactive ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="inactiveSwitch">Inactive Product</label>
                            <p class="text-muted small mb-0">Product will not be visible in store</p>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="need_prescription" id="prescriptionSwitch" value="1" {{ $item->need_prescription ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="prescriptionSwitch">Requires Prescription</label>
                            <p class="text-muted small mb-0">Customer must upload prescription</p>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="not_for_online_sale" id="onlineSaleSwitch" value="1" {{ $item->not_for_online_sale ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="onlineSaleSwitch">Not for Online Sale</label>
                            <p class="text-muted small mb-0">Product available only in physical stores</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="sell_loose" id="looseSwitch" value="1" {{ $item->sell_loose ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="looseSwitch">Sell Loose</label>
                            <p class="text-muted small mb-0">Allow selling individual units</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Batch Management
let batchIndex = {{ $item->batches->count() }};

document.getElementById('addBatch')?.addEventListener('click', function() {
    let table = document.querySelector('#batchTable tbody');
    let row = `
        <tr>
            <td><input type="text" name="batches[${batchIndex}][batch_code]" class="form-control form-control-sm" placeholder="Batch No"></td>
            <td><input type="number" name="batches[${batchIndex}][stock]" class="form-control form-control-sm" placeholder="0"></td>
            <td><input type="date" name="batches[${batchIndex}][expiry_date]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="batches[${batchIndex}][mrp]" class="form-control form-control-sm" placeholder="0.00"></td>
            <td><input type="number" step="0.01" name="batches[${batchIndex}][ptr]" class="form-control form-control-sm" placeholder="0.00"></td>
            <td><input type="number" step="0.01" name="batches[${batchIndex}][discount]" class="form-control form-control-sm" placeholder="0"></td>
            <td><input type="number" step="0.01" name="batches[${batchIndex}][margin]" class="form-control form-control-sm" placeholder="0"></td>
            <td><input type="number" step="0.01" name="batches[${batchIndex}][markup]" class="form-control form-control-sm" placeholder="0"></td>
            <td><input type="number" step="0.01" name="batches[${batchIndex}][selling_price]" class="form-control form-control-sm" placeholder="0.00"></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger removeRow"><i class="bi bi-trash"></i></button></td>
        </tr>
    `;
    table.insertAdjacentHTML('beforeend', row);
    batchIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.removeRow')) {
        e.target.closest('tr').remove();
    }
});

// Main Image Handling
const mainImageInput = document.getElementById('mainImageInput');
const changeMainImageBtn = document.getElementById('changeMainImageBtn');
const removeMainImageBtn = document.getElementById('removeMainImageBtn');
const mainImagePlaceholder = document.getElementById('mainImagePlaceholder');
const mainImagePreviewExisting = document.getElementById('mainImagePreviewExisting');
const mainImageImgNew = document.getElementById('mainImageImgNew');
const removeNewMainImageBtn = document.getElementById('removeNewMainImageBtn');

if (changeMainImageBtn) {
    changeMainImageBtn.addEventListener('click', () => mainImageInput.click());
}

if (mainImagePlaceholder) {
    mainImagePlaceholder.addEventListener('click', () => mainImageInput.click());
}

if (mainImageInput) {
    mainImageInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                if (mainImagePlaceholder) {
                    mainImagePlaceholder.style.display = 'none';
                }
                if (mainImagePreviewExisting) {
                    mainImagePreviewExisting.style.display = 'block';
                    mainImageImgNew.src = event.target.result;
                }
                // Remove existing hidden input if present
                const existingHidden = document.querySelector('input[name="existing_main_image"]');
                if (existingHidden) existingHidden.remove();
            };
            reader.readAsDataURL(file);
        }
    });
}

if (removeMainImageBtn) {
    removeMainImageBtn.addEventListener('click', () => {
        let removeFlag = document.querySelector('input[name="remove_main_image"]');
        if (!removeFlag) {
            removeFlag = document.createElement('input');
            removeFlag.type = 'hidden';
            removeFlag.name = 'remove_main_image';
            removeFlag.value = '1';
            document.getElementById('editProductForm').appendChild(removeFlag);
        }
        // Hide the current main image display
        const mainImageContainer = document.querySelector('#mainImagePreview .position-relative');
        if (mainImageContainer) mainImageContainer.style.display = 'none';
    });
}

if (removeNewMainImageBtn) {
    removeNewMainImageBtn.addEventListener('click', () => {
        mainImageInput.value = '';
        if (mainImagePlaceholder) mainImagePlaceholder.style.display = 'block';
        if (mainImagePreviewExisting) mainImagePreviewExisting.style.display = 'none';
        mainImageImgNew.src = '';
    });
}

// Gallery Images Handling
const galleryInput = document.getElementById('galleryImagesInput');
const newGalleryGrid = document.getElementById('newGalleryGrid');
const newGalleryContainer = document.getElementById('newGalleryContainer');
let newGalleryFiles = [];

function updateNewGalleryPreview() {
    if (!newGalleryGrid) return;
    newGalleryGrid.innerHTML = '';
    if (newGalleryFiles.length === 0) {
        if (newGalleryContainer) newGalleryContainer.style.display = 'none';
        return;
    }
    if (newGalleryContainer) newGalleryContainer.style.display = 'block';
    
    newGalleryFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const col = document.createElement('div');
            col.className = 'col-md-4';
            col.innerHTML = `
                <div class="position-relative">
                    <img src="${e.target.result}" class="img-fluid rounded-3 border shadow-sm" style="height: 100px; width: 100%; object-fit: cover;">
                    <button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-1 remove-new-gallery" data-index="${index}" style="width: 24px; height: 24px; padding: 0; font-size: 12px;">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            `;
            newGalleryGrid.appendChild(col);
            col.querySelector('.remove-new-gallery').addEventListener('click', () => {
                newGalleryFiles.splice(index, 1);
                updateNewGalleryPreview();
                updateGalleryFileInput();
            });
        };
        reader.readAsDataURL(file);
    });
}

function updateGalleryFileInput() {
    const dataTransfer = new DataTransfer();
    newGalleryFiles.forEach(file => dataTransfer.items.add(file));
    if (galleryInput) galleryInput.files = dataTransfer.files;
}

if (galleryInput) {
    galleryInput.addEventListener('change', (e) => {
        const newFiles = Array.from(e.target.files);
        newFiles.forEach(file => {
            if (file.type.startsWith('image/')) newGalleryFiles.push(file);
        });
        updateNewGalleryPreview();
        updateGalleryFileInput();
    });
}

// Existing Gallery Removal
const deletedIds = new Set();
const deletedGalleryContainer = document.getElementById('deletedGalleryIds');

function updateDeletedInput() {
    if (deletedGalleryContainer) {
        deletedGalleryContainer.value = JSON.stringify(Array.from(deletedIds).map(item => JSON.parse(item)));
    }
}

document.querySelectorAll('.remove-existing-gallery').forEach(btn => {
    btn.addEventListener('click', (e) => {
        const item = btn.closest('.existing-gallery-item');
        if (item) {
            const id = item.dataset.id;
            const path = item.dataset.path;
            deletedIds.add(JSON.stringify({ id, path }));
            updateDeletedInput();
            item.remove();
        }
    });
});

// Product Highlights
const highlightsWrapper = document.getElementById('highlightsWrapper');
const addHighlightBtn = document.getElementById('addHighlightBtn');

function updateHighlightButtons() {
    const removes = document.querySelectorAll('.remove-highlight');
    removes.forEach((btn, i) => {
        btn.disabled = removes.length === 1;
    });
}

if (addHighlightBtn) {
    addHighlightBtn.addEventListener('click', () => {
        const div = document.createElement('div');
        div.className = 'highlight-item mb-2';
        div.innerHTML = `
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                    <i class="bi bi-dot text-primary fs-4"></i>
                </span>
                <input type="text" name="product_highlights[]" class="form-control border-start-0" placeholder="e.g., Doctor recommended">
                <button type="button" class="btn btn-outline-danger remove-highlight"><i class="bi bi-trash"></i></button>
            </div>
        `;
        highlightsWrapper.appendChild(div);
        updateHighlightButtons();
    });
}

if (highlightsWrapper) {
    highlightsWrapper.addEventListener('click', (e) => {
        const btn = e.target.closest('.remove-highlight');
        if (btn) {
            btn.closest('.highlight-item').remove();
            updateHighlightButtons();
        }
    });
}

updateHighlightButtons();

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
</script>

<style>
.cursor-pointer {
    cursor: pointer;
}
.rounded-4 {
    border-radius: 1rem !important;
}
.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
}
.table-responsive {
    border-radius: 0.5rem;
}
.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}
.main-image-container {
    transition: all 0.3s ease;
}
.btn-outline-danger {
    border-color: #dc3545;
}
.btn-outline-danger:hover {
    background-color: #dc3545;
    color: white;
}
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

@endsection