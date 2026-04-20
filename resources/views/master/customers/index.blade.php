@extends('layouts.master')

@section('title', 'Customers')

@section('content')

<div class="container-fluid px-4 py-3">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-users text-primary me-2"></i>Customers
            </h1>
            <p class="text-muted small mb-0">Manage your customer database</p>
        </div>
        <a href="{{ route('customers.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus-circle me-2"></i>Add Customer
        </a>
    </div>

    <!-- Stats Cards - Lighter Colors -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Customers</h6>
                            <h2 class="mb-0 fw-bold text-primary">{{ $customers->total() }}</h2>
                        </div>
                        <div class="rounded-3 p-3" style="background: #eef2ff;">
                            <i class="fas fa-users fa-2x text-primary"></i>
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
                            <h6 class="text-muted mb-1">VIP Customers</h6>
                            <h2 class="mb-0 fw-bold text-warning">{{ $customers->where('customer_type', 'vip')->count() }}</h2>
                        </div>
                        <div class="rounded-3 p-3" style="background: #fefce8;">
                            <i class="fas fa-crown fa-2x text-warning"></i>
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
                            <h6 class="text-muted mb-1">Avg. Discount</h6>
                            <h2 class="mb-0 fw-bold text-success">{{ round($customers->avg('discount') ?? 0) }}%</h2>
                        </div>
                        <div class="rounded-3 p-3" style="background: #ecfdf5;">
                            <i class="fas fa-percent fa-2x text-success"></i>
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
                            <h6 class="text-muted mb-1">Active (30 days)</h6>
                            <h2 class="mb-0 fw-bold text-info">{{ $customers->where('last_buy_date', '>=', now()->subDays(30))->count() }}</h2>
                        </div>
                        <div class="rounded-3 p-3" style="background: #ecfeff;">
                            <i class="fas fa-calendar-check fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <form method="GET" action="{{ route('customers.index') }}" id="filterForm">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <div class="position-relative">
                            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" 
                                   name="search" 
                                   class="form-control rounded-pill ps-5" 
                                   placeholder="Search by name, contact..." 
                                   value="{{ request('search') }}"
                                   style="background: #f8f9fa;">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="customer_type" class="form-select rounded-pill" style="background: #f8f9fa;" onchange="this.form.submit()">
                            <option value="all" {{ request('customer_type') == 'all' ? 'selected' : '' }}>All Types</option>
                            <option value="vip" {{ request('customer_type') == 'vip' ? 'selected' : '' }}>👑 VIP</option>
                            <option value="regular" {{ request('customer_type') == 'regular' ? 'selected' : '' }}>👤 Regular</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="doctor_id" class="form-select rounded-pill" style="background: #f8f9fa;" onchange="this.form.submit()">
                            <option value="">All Doctors</option>
                            @foreach($doctors ?? [] as $doctor)
                                <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    👨‍⚕️ {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="discount_range" class="form-select rounded-pill" style="background: #f8f9fa;" onchange="this.form.submit()">
                            <option value="">All Discounts</option>
                            <option value="0" {{ request('discount_range') == '0' ? 'selected' : '' }}>0% Discount</option>
                            <option value="1_10" {{ request('discount_range') == '1_10' ? 'selected' : '' }}>🏷️ 1-10%</option>
                            <option value="11_20" {{ request('discount_range') == '11_20' ? 'selected' : '' }}>🏷️ 11-20%</option>
                            <option value="21_plus" {{ request('discount_range') == '21_plus' ? 'selected' : '' }}>🏷️ 21%+</option>
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
                        @if(request()->anyFilled(['search', 'customer_type', 'doctor_id', 'discount_range']))
                            <a href="{{ route('customers.index') }}" class="btn btn-light rounded-pill w-100">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers
                        </small>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3 rounded-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-3 rounded-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-uppercase small text-muted">
                            <th width="60" class="py-3 fw-semibold">
                                <i class="fas fa-hashtag me-1"></i> ID
                            </th>
                            <th class="py-3 fw-semibold">
                                <i class="fas fa-user me-1"></i> Customer
                            </th>
                            <th class="py-3 fw-semibold">
                                <i class="fas fa-phone me-1"></i> Contact
                            </th>
                            <th class="py-3 fw-semibold">
                                <i class="fas fa-tag me-1"></i> Type
                            </th>
                            <th class="py-3 fw-semibold">
                                <i class="fas fa-user-md me-1"></i> Doctor
                            </th>
                            <th class="py-3 fw-semibold">
                                <i class="fas fa-city me-1"></i> City
                            </th>
                            <th class="py-3 fw-semibold">
                                <i class="fas fa-percent me-1"></i> Discount
                            </th>
                            <th class="py-3 fw-semibold">
                                <i class="fas fa-calendar me-1"></i> Last Purchase
                            </th>
                            <th width="120" class="py-3 fw-semibold text-center">
                                <i class="fas fa-cog me-1"></i> Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td class="fw-semibold text-muted">#{{ $customer->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $customer->name }}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-envelope me-1"></i>{{ $customer->email ?? 'No email' }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>
                                        <i class="fas fa-phone-alt text-muted me-2"></i>
                                        {{ $customer->contact ?? '—' }}
                                    </span>
                                    @if($customer->alt_contact)
                                        <small class="text-muted">
                                            <i class="fas fa-mobile-alt me-2"></i>{{ $customer->alt_contact }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($customer->customer_type == 'vip')
                                    <span class="badge-vip">
                                        <i class="fas fa-crown me-1"></i>VIP
                                    </span>
                                @else
                                    <span class="badge-regular">
                                        <i class="fas fa-user me-1"></i>Regular
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($customer->doctor && $customer->doctor->name)
                                    <div>
                                        <i class="fas fa-user-md text-muted me-1"></i>
                                        {{ $customer->doctor->name }}
                                        @if($customer->doctor->specialization)
                                            <br><small class="text-muted">{{ $customer->doctor->specialization }}</small>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($customer->city)
                                    <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                    {{ $customer->city }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($customer->discount > 0)
                                    <div class="discount-badge">
                                        <i class="fas fa-tag me-1"></i>{{ $customer->discount }}% OFF
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($customer->last_buy_date)
                                    <div class="d-flex flex-column">
                                        <span>
                                            <i class="far fa-calendar-alt text-muted me-1"></i>
                                            {{ \Carbon\Carbon::parse($customer->last_buy_date)->format('d M Y') }}
                                        </span>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($customer->last_buy_date)->diffForHumans() }}
                                        </small>
                                    </div>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-shopping-cart me-1"></i>No purchases
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('customers.edit', $customer->id) }}" 
                                       class="btn-action btn-edit" 
                                       data-bs-toggle="tooltip" 
                                       title="Edit Customer">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('customers.destroy', $customer->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('⚠️ Are you sure you want to delete {{ addslashes($customer->name) }}? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn-action btn-delete" 
                                                data-bs-toggle="tooltip" 
                                                title="Delete Customer">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-users-slash fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Customers Found</h5>
                                    <p class="text-muted small mb-3">Get started by adding your first customer</p>
                                    <a href="{{ route('customers.create') }}" class="btn btn-primary rounded-pill">
                                        <i class="fas fa-plus me-2"></i>Add Customer
                                    </a>
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
                    Showing <strong>{{ $customers->firstItem() ?? 0 }}</strong> to <strong>{{ $customers->lastItem() ?? 0 }}</strong> 
                    of <strong>{{ $customers->total() }}</strong> results
                </div>
                <div>
                    {{ $customers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Avatar Circle - Lighter */
    .avatar-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    /* Badges - Modern */
    .badge-vip {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .badge-regular {
        background: #f1f5f9;
        color: #475569;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .discount-badge {
        background: #dcfce7;
        color: #166534;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        width: fit-content;
    }

    /* Action Buttons - Modern */
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
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Card Improvements */
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none !important;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
    }

    /* Stats Cards */
    .stats-card {
        transition: transform 0.2s;
        cursor: pointer;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
    }

    /* Custom Scrollbar */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Form Controls */
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

@endsection