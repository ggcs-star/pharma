@extends('layouts.master')

@section('title', 'Customers')

@section('content')

<div class="container-fluid px-4 py-3">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Customers</h1>
            <p class="text-muted small mb-0">Manage your customer database</p>
        </div>
        <a href="{{ route('customers.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus-circle me-2"></i>Add Customer
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Customers</h6>
                            <h2 class="mb-0 fw-bold">{{ $customers->total() }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">VIP Customers</h6>
                            <h2 class="mb-0 fw-bold">{{ $customers->where('customer_type', 'vip')->count() }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="fas fa-crown fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Avg. Discount</h6>
                            <h2 class="mb-0 fw-bold">{{ round($customers->avg('discount') ?? 0) }}%</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="fas fa-percent fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Active (30 days)</h6>
                            <h2 class="mb-0 fw-bold">{{ $customers->where('last_buy_date', '>=', now()->subDays(30))->count() }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex gap-3">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted small"></i>
                        <input type="text" id="searchInput" class="form-control rounded-pill ps-5" placeholder="Search customers..." style="width: 250px;">
                    </div>
                    <div>
                        <select id="typeFilter" class="form-select rounded-pill">
                            <option value="all">All Types</option>
                            <option value="vip">VIP</option>
                            <option value="regular">Regular</option>
                        </select>
                    </div>
                </div>
                <div class="text-muted small">
                    <i class="fas fa-sync-alt me-1"></i> Last updated: {{ now()->format('d M Y, h:i A') }}
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3 rounded-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="customersTable">
                    <thead class="bg-light">
                        <tr class="text-uppercase small text-muted">
                            <th width="60" class="py-3 fw-semibold">ID</th>
                            <th class="py-3 fw-semibold">Customer</th>
                            <th class="py-3 fw-semibold">Contact</th>
                            <th class="py-3 fw-semibold">Type</th>
                            <th class="py-3 fw-semibold">Doctor</th>
                            <th class="py-3 fw-semibold">City</th>
                            <th class="py-3 fw-semibold">Discount</th>
                            <th class="py-3 fw-semibold">Last Purchase</th>
                            <th width="120" class="py-3 fw-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr class="customer-row" data-type="{{ $customer->customer_type }}" data-name="{{ strtolower($customer->name) }}" data-contact="{{ $customer->contact }}">
                            <td class="fw-semibold text-muted">#{{ $customer->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle bg-primary bg-opacity-10 text-primary">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $customer->name }}</div>
                                        <small class="text-muted">{{ $customer->email ?? 'No email' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span><i class="fas fa-phone-alt text-muted me-2 small"></i>{{ $customer->contact }}</span>
                                    @if($customer->alt_contact)
                                        <small class="text-muted">{{ $customer->alt_contact }}</small>
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
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-user-md text-muted"></i>
                                        <span>{{ $customer->doctor->name }}</span>
                                    </div>
                                    @if($customer->doctor->specialization)
                                        <small class="text-muted ms-4">{{ $customer->doctor->specialization }}</small>
                                    @endif
                                @else
                                    <span class="text-muted fst-italic">—</span>
                                @endif
                            </td>
                            <td>
                                @if($customer->city)
                                    <i class="fas fa-location-dot text-muted me-1"></i>
                                    {{ $customer->city }}
                                @else
                                    <span class="text-muted fst-italic">—</span>
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
                                        <span><i class="far fa-calendar-alt text-muted me-1"></i>{{ \Carbon\Carbon::parse($customer->last_buy_date)->format('d M Y') }}</span>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($customer->last_buy_date)->diffForHumans() }}</small>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">No purchases</span>
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
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn-action btn-delete" 
                                                onclick="return confirm('⚠️ Are you sure you want to delete {{ addslashes($customer->name) }}? This action cannot be undone.')"
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
                    Showing <span id="showingStart">{{ $customers->firstItem() ?? 0 }}</span> to <span id="showingEnd">{{ $customers->lastItem() ?? 0 }}</span> of <span id="totalCount">{{ $customers->total() }}</span> results
                </div>
                <div>
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Custom Gradients */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    /* Avatar Circle */
    .avatar-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
    }

    /* Badges */
    .badge-vip {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #78350f;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .badge-regular {
        background: #e2e8f0;
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
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8fafc;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    /* Card Improvements */
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
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
</style>
@endpush

@push('scripts')
<script>
    // Live search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const typeFilter = document.getElementById('typeFilter');
        const rows = document.querySelectorAll('.customer-row');
        const showingStart = document.getElementById('showingStart');
        const showingEnd = document.getElementById('showingEnd');
        const totalCount = document.getElementById('totalCount');
        
        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const typeValue = typeFilter.value;
            let visibleCount = 0;
            
            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const contact = row.getAttribute('data-contact');
                const type = row.getAttribute('data-type');
                
                const matchesSearch = name.includes(searchTerm) || contact.includes(searchTerm);
                const matchesType = typeValue === 'all' || type === typeValue;
                
                if (matchesSearch && matchesType) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update showing info
            if (showingStart && showingEnd && totalCount) {
                showingStart.textContent = visibleCount > 0 ? '1' : '0';
                showingEnd.textContent = visibleCount;
                // Note: This is simplified; in production, you'd want pagination-aware filtering
            }
        }
        
        if (searchInput) {
            searchInput.addEventListener('keyup', filterTable);
        }
        if (typeFilter) {
            typeFilter.addEventListener('change', filterTable);
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