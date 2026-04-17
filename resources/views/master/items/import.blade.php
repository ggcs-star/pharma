@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            {{-- Header Section --}}
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">Import Medicines CSV</h3>
                    <p class="text-muted mb-0">Upload and import medicine data from CSV or Excel files</p>
                </div>
            </div>

            {{-- ✅ ERROR MESSAGE --}}
            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center shadow-sm border-start border-danger border-4" role="alert">
                    <div class="flex-shrink-0 me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <div>
                        <strong>Error!</strong> {{ session('error') }}
                    </div>
                </div>
            @endif

            {{-- ✅ SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center shadow-sm border-start border-success border-4" role="alert">
                    <div class="flex-shrink-0 me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div>
                        <strong>Success!</strong> {{ session('success') }}
                    </div>
                </div>
            @endif

            {{-- ✅ VALIDATION ERROR --}}
            @if($errors->any())
                <div class="alert alert-warning d-flex align-items-center shadow-sm border-start border-warning border-4" role="alert">
                    <div class="flex-shrink-0 me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <div>
                        <strong>Validation Error!</strong> {{ $errors->first() }}
                    </div>
                </div>
            @endif

            {{-- Main Import Card --}}
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="card-title fw-semibold mb-2">Upload File</h5>
                    <p class="text-muted small mb-0">Supported formats: .csv, .xlsx, .xls</p>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('master.items.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- File Upload Area --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                    <polyline points="13 2 13 9 20 9"></polyline>
                                </svg>
                                Select CSV / Excel File
                            </label>
                            
                            <div class="file-upload-wrapper border rounded-3 p-4 text-center bg-light">
                                <div class="mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-primary opacity-50">
                                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                        <polyline points="13 2 13 9 20 9"></polyline>
                                    </svg>
                                </div>
                                
                                <input type="file" 
                                       name="file" 
                                       class="form-control" 
                                       accept=".csv,.xlsx,.xls" 
                                       required
                                       style="max-width: 300px; margin: 0 auto;">
                                
                                <small class="text-muted d-block mt-2">
                                    Drag & drop your file here or click to browse
                                </small>
                            </div>
                        </div>

                        {{-- Information Box --}}
                        <div class="alert alert-info bg-info bg-opacity-10 border-0 rounded-3 mb-4">
                            <div class="d-flex">
                                <div class="me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                </div>
                                <div>
                                    <strong>Important Notes:</strong>
                                    <ul class="mb-0 mt-1 small">
                                        <li>Ensure your CSV/Excel file has proper column headers</li>
                                        <li>Large files may take a few moments to process</li>
                                        <li>Duplicate entries will be skipped automatically</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('master.items.index') }}" class="btn btn-light px-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                    <line x1="19" y1="12" x2="5" y2="12"></line>
                                    <polyline points="12 19 5 12 12 5"></polyline>
                                </svg>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Upload & Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Template Download Section --}}
            <div class="text-center mt-4">
                <p class="text-muted mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Don't have a template?
                    <a href="#" class="text-decoration-none">Download sample CSV template</a>
                </p>
            </div>

        </div>
    </div>
</div>

<style>
.file-upload-wrapper {
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-upload-wrapper:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6 !important;
}

.alert {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateY(-10px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

@endsection