@extends('layouts.master')

@section('title', 'Doctors')

@section('content')

<div class="container-fluid px-4 py-3">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-user-md text-primary me-2"></i>Doctors
            </h1>
            <p class="text-muted small mb-0">Manage your doctor database</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#doctorModal">
            <i class="fas fa-plus-circle me-2"></i>Add Doctor
        </button>
    </div>

    <!-- Stats Cards -->
    @php
        $totalDoctors = \App\Models\Doctor::count();
        $totalSpecialities = \App\Models\Doctor::whereNotNull('medical_speciality')->distinct('medical_speciality')->count('medical_speciality');
        $totalCities = \App\Models\Doctor::whereNotNull('clinic_city')->distinct('clinic_city')->count('clinic_city');
        $activeDoctors = \App\Models\Doctor::count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Doctors</h6>
                            <h2 class="mb-0 fw-bold text-primary">{{ $totalDoctors }}</h2>
                        </div>
                        <div class="rounded-3 p-3" style="background: #eef2ff;">
                            <i class="fas fa-user-md fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Specialities</h6>
                            <h2 class="mb-0 fw-bold text-success">{{ $totalSpecialities }}</h2>
                        </div>
                        <div class="rounded-3 p-3" style="background: #ecfdf5;">
                            <i class="fas fa-stethoscope fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Cities</h6>
                            <h2 class="mb-0 fw-bold text-info">{{ $totalCities }}</h2>
                        </div>
                        <div class="rounded-3 p-3" style="background: #ecfeff;">
                            <i class="fas fa-city fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Active</h6>
                            <h2 class="mb-0 fw-bold text-warning">{{ $activeDoctors }}</h2>
                        </div>
                        <div class="rounded-3 p-3" style="background: #fefce8;">
                            <i class="fas fa-check-circle fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <form method="GET" action="{{ route('doctors.index') }}" id="filterForm">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <div class="position-relative">
                            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" 
                                   name="search" 
                                   class="form-control rounded-pill ps-5" 
                                   placeholder="Search by name, contact, speciality..." 
                                   value="{{ request('search') }}"
                                   style="background: #f8f9fa;">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="speciality" class="form-select rounded-pill" style="background: #f8f9fa;" onchange="this.form.submit()">
                            <option value="all">All Specialities</option>
                            @foreach($specialities as $speciality)
                                <option value="{{ $speciality }}" {{ request('speciality') == $speciality ? 'selected' : '' }}>
                                    🏥 {{ $speciality }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="position-relative">
                            <i class="fas fa-map-marker-alt position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" 
                                   name="city" 
                                   class="form-control rounded-pill ps-5" 
                                   placeholder="Filter by city..." 
                                   value="{{ request('city') }}"
                                   style="background: #f8f9fa;">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="sort_by" class="form-select rounded-pill" style="background: #f8f9fa;" onchange="this.form.submit()">
                            <option value="id" {{ request('sort_by') == 'id' ? 'selected' : '' }}>Sort by ID</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Sort by Name</option>
                            <option value="medical_speciality" {{ request('sort_by') == 'medical_speciality' ? 'selected' : '' }}>Sort by Speciality</option>
                            <option value="clinic_city" {{ request('sort_by') == 'clinic_city' ? 'selected' : '' }}>Sort by City</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="per_page" class="form-select rounded-pill" style="background: #f8f9fa;" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        @if(request()->anyFilled(['search', 'speciality', 'city']))
                            <a href="{{ route('doctors.index') }}" class="btn btn-light rounded-pill w-100">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Showing {{ $doctors->firstItem() ?? 0 }} to {{ $doctors->lastItem() ?? 0 }} of {{ $doctors->total() }} doctors
                        </small>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3 rounded-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-3 rounded-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-uppercase small text-muted">
                            <th width="60" class="py-3"><i class="fas fa-hashtag me-1"></i> ID</th>
                            <th class="py-3"><i class="fas fa-user me-1"></i> Doctor</th>
                            <th class="py-3"><i class="fas fa-phone me-1"></i> Contact</th>
                            <th class="py-3"><i class="fas fa-stethoscope me-1"></i> Speciality</th>
                            <th class="py-3"><i class="fas fa-hospital me-1"></i> Clinic</th>
                            <th class="py-3"><i class="fas fa-map-marker-alt me-1"></i> Location</th>
                            <th width="120" class="py-3 text-center"><i class="fas fa-cog me-1"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($doctors as $doctor)
                        <tr>
                            <td class="fw-semibold text-muted">#{{ $doctor->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="doctor-avatar">
                                        <i class="fas fa-user-md"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $doctor->name }}</div>
                                        @if($doctor->registration_number)
                                            <small class="text-muted">
                                                <i class="fas fa-id-card me-1"></i>Reg: {{ $doctor->registration_number }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    @if($doctor->contact)
                                        <span><i class="fas fa-phone-alt text-muted me-2"></i>{{ $doctor->contact }}</span>
                                    @endif
                                    @if($doctor->email)
                                        <small class="text-muted">
                                            <i class="fas fa-envelope text-muted me-2"></i>{{ $doctor->email }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($doctor->medical_speciality)
                                    <span class="speciality-badge">
                                        <i class="fas fa-stethoscope me-1"></i>{{ $doctor->medical_speciality }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($doctor->clinic_name)
                                    <div>
                                        <i class="fas fa-clinic-medical text-muted me-1"></i>
                                        <strong>{{ $doctor->clinic_name }}</strong>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($doctor->clinic_city)
                                    <div>
                                        <i class="fas fa-city text-muted me-1"></i>
                                        {{ $doctor->clinic_city }}
                                        @if($doctor->clinic_pincode)
                                            <br><small class="text-muted">PIN: {{ $doctor->clinic_pincode }}</small>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('doctors.edit', $doctor->id) }}" 
                                       class="btn-action btn-edit" 
                                       data-bs-toggle="tooltip" 
                                       title="Edit Doctor">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('doctors.destroy', $doctor->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('⚠️ Are you sure you want to delete Dr. {{ addslashes($doctor->name) }}? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn-action btn-delete" 
                                                data-bs-toggle="tooltip" 
                                                title="Delete Doctor">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-user-md-slash fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Doctors Found</h5>
                                    <p class="text-muted small mb-3">Get started by adding your first doctor</p>
                                    <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#doctorModal">
                                        <i class="fas fa-plus me-2"></i>Add Doctor
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center p-4 border-top">
                <div class="small text-muted">
                    <i class="fas fa-chart-line me-1"></i>
                    Showing <strong>{{ $doctors->firstItem() ?? 0 }}</strong> to <strong>{{ $doctors->lastItem() ?? 0 }}</strong> 
                    of <strong>{{ $doctors->total() }}</strong> results
                </div>
                <div>
                    {{ $doctors->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Doctor Modal -->
<div class="modal fade" id="doctorModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('doctors.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title">
                        <i class="fas fa-user-md me-2"></i>Add New Doctor
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user text-primary me-1"></i> Name *
                            </label>
                            <input type="text" name="name" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-phone text-primary me-1"></i> Contact
                            </label>
                            <input type="text" name="contact" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-envelope text-primary me-1"></i> Email
                            </label>
                            <input type="email" name="email" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-id-card text-primary me-1"></i> Registration Number
                            </label>
                            <input type="text" name="registration_number" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-graduation-cap text-primary me-1"></i> Professional Credential
                            </label>
                            <input type="text" name="professional_credential" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-stethoscope text-primary me-1"></i> Medical Speciality
                            </label>
                            <input type="text" name="medical_speciality" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-hospital text-primary me-1"></i> Clinic Name
                            </label>
                            <input type="text" name="clinic_name" class="form-control rounded-3">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-city text-primary me-1"></i> Clinic City
                            </label>
                            <input type="text" name="clinic_city" class="form-control rounded-3">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-mail-bulk text-primary me-1"></i> Clinic Pincode
                            </label>
                            <input type="text" name="clinic_pincode" class="form-control rounded-3">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-map-marker-alt text-primary me-1"></i> Clinic Address
                            </label>
                            <textarea name="clinic_address" class="form-control rounded-3" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-1"></i>Add Doctor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Doctor Avatar */
    .doctor-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 20px;
    }

    /* Speciality Badge */
    .speciality-badge {
        background: #eef2ff;
        color: #4338ca;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Action Buttons */
    .btn-action {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
        background: transparent;
        text-decoration: none;
    }
    
    .btn-edit {
        color: #f59e0b;
        background: #fffbeb;
    }
    
    .btn-edit:hover {
        background: #fef3c7;
        transform: translateY(-2px);
        color: #d97706;
    }
    
    .btn-delete {
        color: #ef4444;
        background: #fef2f2;
    }
    
    .btn-delete:hover {
        background: #fee2e2;
        transform: translateY(-2px);
        color: #dc2626;
    }

    /* Empty State */
    .empty-state {
        padding: 40px 20px;
    }

    /* Table Styling */
    .table-hover tbody tr {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Card Improvements */
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
    }

    /* Form Controls */
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Modal Improvements */
    .modal-content {
        border: none;
    }
    
    .rounded-top-4 {
        border-top-left-radius: 1rem !important;
        border-top-right-radius: 1rem !important;
    }
    
    .rounded-bottom-4 {
        border-bottom-left-radius: 1rem !important;
        border-bottom-right-radius: 1rem !important;
    }

    /* Pagination Styling */
    .pagination {
        margin-bottom: 0;
    }
    
    .page-link {
        border: none;
        color: #475569;
        border-radius: 8px !important;
        margin: 0 2px;
    }
    
    .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .page-link:hover {
        background-color: #f1f5f9;
        color: #1e293b;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-submit form on filter change
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit when dropdowns change
        const filterSelects = document.querySelectorAll('#filterForm select');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });
        
        // Search with debounce
        let searchTimeout;
        const searchInput = document.querySelector('input[name="search"]');
        if(searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            });
        }
        
        // City filter with debounce
        const cityInput = document.querySelector('input[name="city"]');
        if(cityInput) {
            cityInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            });
        }
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

@endsection