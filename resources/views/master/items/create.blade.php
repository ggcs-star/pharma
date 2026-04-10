@extends('layouts.master')

@section('title', 'Add New Product')

@section('content')

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold text-dark">Create New Product</h1>
            <p class="text-muted mt-1">Add product details, media, and inventory information</p>
        </div>
        <div>
            <a href="{{ route('master.items.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Cancel
            </a>
            <button type="submit" form="productForm" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> Save Product
            </button>
        </div>
    </div>
<hr class="my-3">

<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-header bg-white border-0 pt-3 pb-0">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-shield-lock-fill text-danger me-2"></i>
            Prescription Settings
        </h6>
        <small class="text-muted">Control medicine safety & restrictions</small>
    </div>

    <div class="card-body">

        <!-- Prescription Required -->
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" 
                   name="need_prescription" id="needPrescription">
            <label class="form-check-label fw-semibold" for="needPrescription">
                Prescription Required (Rx)
            </label>
            <div class="text-muted small">
                User must upload prescription before checkout
            </div>
        </div>

        <!-- Not for Online Sale -->
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" 
                   name="not_for_online_sale" id="notForOnline">
            <label class="form-check-label fw-semibold" for="notForOnline">
                Not for Online Sale
            </label>
            <div class="text-muted small">
                Product cannot be purchased online
            </div>
        </div>

        <!-- Schedule Auto Logic -->
        <div class="alert alert-warning py-2 px-3 small mb-0">
            💡 Tip: Schedule H / H1 medicines usually require prescription
        </div>

    </div>
</div>
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('master.items.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf

        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Basic Information Card -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h5 class="card-title fw-bold mb-0">
                            <i class="bi bi-info-circle-fill text-primary me-2"></i>
                            Basic Information
                        </h5>
                        <p class="text-muted small mt-1">Essential product details and categorization</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-lg" placeholder="Enter product name" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Manufacturer <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select name="manufacturer_id" id="manufacturerDropdown" class="form-select" required>
                                        <option value="">Select Manufacturer</option>
                                        @foreach($manufacturers as $manufacturer)
                                            <option value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#manufacturerModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Brand</label>
                                <input type="text" name="brand" class="form-control" placeholder="Enter brand name">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select name="category_id" id="categoryDropdown" class="form-select" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Sub Category</label>
                                <div class="input-group">
                                    <select name="sub_category_id" id="subCategoryDropdown" class="form-select">
                                        <option value="">Select Sub Category</option>
                                        @foreach($subCategories as $sub)
                                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#subCategoryModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Schedule Type</label>
                                <select name="sch_type" class="form-select">
                                    <option value="">Select Schedule</option>
                                    <option value="OTC">OTC</option>
                                    <option value="H">Schedule H</option>
                                    <option value="H1">Schedule H1</option>
                                    <option value="X">Schedule X</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Media Card -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h5 class="card-title fw-bold mb-0">
                            <i class="bi bi-image-fill text-primary me-2"></i>
                            Product Media
                        </h5>
                        <p class="text-muted small mt-1">Upload main image and gallery photos</p>
                    </div>
                    <div class="card-body p-4">
                        <!-- Main Image Section -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Main Image <span class="text-danger">*</span></label>
                            <div class="main-image-upload">
                                <input type="file" name="main_image" id="mainImageInput" class="d-none" accept="image/*" required>
                                <div id="mainImagePreviewContainer" class="text-center">
                                    <div id="mainImagePlaceholder" class="border rounded-3 p-5 bg-light cursor-pointer" style="cursor: pointer;">
                                        <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                                        <p class="mt-2 mb-0 text-muted">Click to upload main image</p>
                                        <small class="text-muted">JPG, PNG, WEBP up to 5MB</small>
                                    </div>
                                    <div id="mainImagePreview" style="display: none;">
                                        <div class="position-relative d-inline-block">
                                            <img id="mainImageImg" src="" alt="Main Image" class="img-fluid rounded-3 border" style="max-height: 300px;">
                                            <button type="button" id="removeMainImageBtn" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-2">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gallery Images Section -->
                        <div>
                            <label class="form-label fw-semibold">Gallery Images</label>
                            <input type="file" name="gallery_images[]" id="galleryImagesInput" class="form-control mb-3" multiple accept="image/*">
                            <div id="galleryGrid" class="row g-3 mt-2"></div>
                            <div id="galleryPlaceholder" class="text-center border rounded-3 p-5 bg-light">
                                <i class="bi bi-images fs-1 text-muted"></i>
                                <p class="mt-2 mb-0 text-muted">No gallery images selected</p>
                                <small class="text-muted">Select images to display in product gallery</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Details Card -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h5 class="card-title fw-bold mb-0">
                            <i class="bi bi-file-text-fill text-primary me-2"></i>
                            Product Details
                        </h5>
                        <p class="text-muted small mt-1">Description and key highlights</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Enter detailed product description..."></textarea>
                        </div>

                        <div>
                            <label class="form-label fw-semibold">Product Highlights</label>
                            <div id="highlightsWrapper">
                                <div class="highlight-item mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent"><i class="bi bi-star-fill text-warning"></i></span>
                                        <input type="text" name="highlights[]" class="form-control" placeholder="e.g., 100% genuine medicine">
                                        <button type="button" class="btn btn-outline-danger remove-highlight" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="addHighlightBtn" class="btn btn-outline-primary btn-sm mt-2">
                                <i class="bi bi-plus-lg me-1"></i> Add Highlight
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Inventory & Pricing -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h5 class="card-title fw-bold mb-0">
                            <i class="bi bi-box-seam-fill text-primary me-2"></i>
                            Inventory & Pricing
                        </h5>
                        <p class="text-muted small mt-1">Stock, pricing, and logistics</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pack Type</label>
                            <select name="pack_id" class="form-select">
                                <option value="">Select Pack</option>
                                @foreach($packTypes as $pack)
                                    <option value="{{ $pack->id }}">{{ $pack->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Unit</label>
                            <select name="unit_id" class="form-select">
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Number of Units</label>
                            <input type="number" name="number_of_units" class="form-control" placeholder="Quantity per pack">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">GST %</label>
                            <input type="number" step="0.01" name="gst_percent" class="form-control" placeholder="e.g., 18">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">HSN Code</label>
                            <input type="text" name="hsn_code" class="form-control" placeholder="HSN/SAC code">
                        </div>

                        <hr class="my-3">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Rack Location</label>
                            <input type="text" name="rack" class="form-control" placeholder="Aisle-Shelf-Bin">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Barcode</label>
                            <input type="text" name="barcode" class="form-control" placeholder="Scan code">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Molecule</label>
                            <input type="text" name="molecule" class="form-control" placeholder="Active ingredient">
                        </div>

                        <hr class="my-3">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Min Threshold</label>
                            <input type="number" name="min_threshold" class="form-control" placeholder="Low stock alert">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Max Threshold</label>
                            <input type="number" name="max_threshold" class="form-control" placeholder="Maximum stock">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Max Discount %</label>
                            <input type="number" step="0.01" name="max_discount" class="form-control" placeholder="Max allowed discount">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modals -->
<div class="modal fade" id="manufacturerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Add Manufacturer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="manufacturerName" class="form-control" placeholder="Manufacturer Name">
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveManufacturer">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="categoryName" class="form-control" placeholder="Category Name">
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCategory">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="subCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Add Sub Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select id="subCategoryCategory" class="form-select mb-3">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <input type="text" id="subCategoryName" class="form-control" placeholder="Sub Category Name">
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveSubCategory">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Main Image Upload & Preview
    const mainImageInput = document.getElementById('mainImageInput');
    const mainImagePlaceholderDiv = document.getElementById('mainImagePlaceholder');
    const mainImagePreviewDiv = document.getElementById('mainImagePreview');
    const mainImageImg = document.getElementById('mainImageImg');
    const removeMainImageBtn = document.getElementById('removeMainImageBtn');

    mainImagePlaceholderDiv.addEventListener('click', () => mainImageInput.click());
    mainImageInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                mainImageImg.src = event.target.result;
                mainImagePlaceholderDiv.style.display = 'none';
                mainImagePreviewDiv.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    removeMainImageBtn.addEventListener('click', () => {
        mainImageInput.value = '';
        mainImagePlaceholderDiv.style.display = 'block';
        mainImagePreviewDiv.style.display = 'none';
        mainImageImg.src = '';
    });

    // Gallery Images
    const galleryInput = document.getElementById('galleryImagesInput');
    const galleryGrid = document.getElementById('galleryGrid');
    const galleryPlaceholder = document.getElementById('galleryPlaceholder');
    let galleryFiles = [];

    function updateGallery() {
        galleryGrid.innerHTML = '';
        if (galleryFiles.length === 0) {
            galleryPlaceholder.style.display = 'block';
            return;
        }
        galleryPlaceholder.style.display = 'none';
        
        galleryFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const col = document.createElement('div');
                col.className = 'col-md-6 col-lg-4';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-fluid rounded-3 border shadow-sm" style="height: 150px; width: 100%; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-2" data-index="${index}">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                `;
                galleryGrid.appendChild(col);
                col.querySelector('button').addEventListener('click', () => {
                    galleryFiles.splice(index, 1);
                    updateGallery();
                    updateFileInput();
                });
            };
            reader.readAsDataURL(file);
        });
    }

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        galleryFiles.forEach(file => dataTransfer.items.add(file));
        galleryInput.files = dataTransfer.files;
    }

    galleryInput.addEventListener('change', (e) => {
        const newFiles = Array.from(e.target.files);
        newFiles.forEach(file => {
            if (file.type.startsWith('image/')) galleryFiles.push(file);
        });
        updateGallery();
        updateFileInput();
    });

    updateGallery();

    // Product Highlights
    const highlightsWrapper = document.getElementById('highlightsWrapper');
    const addHighlightBtn = document.getElementById('addHighlightBtn');

    function updateHighlightButtons() {
        const removes = document.querySelectorAll('.remove-highlight');
        removes.forEach((btn, i) => btn.disabled = removes.length === 1);
    }

    addHighlightBtn.addEventListener('click', () => {
        const div = document.createElement('div');
        div.className = 'highlight-item mb-2';
        div.innerHTML = `
            <div class="input-group">
                <span class="input-group-text bg-transparent"><i class="bi bi-star-fill text-warning"></i></span>
                <input type="text" name="highlights[]" class="form-control" placeholder="e.g., Doctor recommended">
                <button type="button" class="btn btn-outline-danger remove-highlight"><i class="bi bi-trash"></i></button>
            </div>
        `;
        highlightsWrapper.appendChild(div);
        updateHighlightButtons();
    });

    highlightsWrapper.addEventListener('click', (e) => {
        const btn = e.target.closest('.remove-highlight');
        if (btn) {
            btn.closest('.highlight-item').remove();
            updateHighlightButtons();
        }
    });

    updateHighlightButtons();

    // Modal Saves (Placeholder AJAX)
    document.getElementById('saveManufacturer')?.addEventListener('click', () => {
        const name = document.getElementById('manufacturerName').value;
        if(name) {
            bootstrap.Modal.getInstance(document.getElementById('manufacturerModal')).hide();
            alert('Manufacturer saved! Implement AJAX to refresh dropdown.');
        }
    });

    document.getElementById('saveCategory')?.addEventListener('click', () => {
        const name = document.getElementById('categoryName').value;
        if(name) {
            bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
            alert('Category saved! Implement AJAX to refresh dropdown.');
        }
    });

    document.getElementById('saveSubCategory')?.addEventListener('click', () => {
        const name = document.getElementById('subCategoryName').value;
        const catId = document.getElementById('subCategoryCategory').value;
        if(name && catId) {
            bootstrap.Modal.getInstance(document.getElementById('subCategoryModal')).hide();
            alert('Sub Category saved! Implement AJAX to refresh dropdown.');
        }
    });
</script>

<style>
    .cursor-pointer {
        cursor: pointer;
    }
    .sticky-top {
        position: sticky;
        top: 20px;
        z-index: 100;
    }
    .rounded-4 {
        border-radius: 1rem !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .btn-primary:hover {
        background-color: #0b5ed7;
    }
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

@endsection