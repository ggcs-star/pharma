@extends('layouts.master')

@section('content')


<div class="main-wrapper">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Purchase Return List</h4>

    <a href="{{ route('purchase-return.create') }}" class="btn btn-success">
        <i class="fa fa-plus"></i> Create Return
    </a>
</div>
    
    <!-- 🔍 FILTER -->
    <form method="GET" class="row mb-3">

        <div class="col-md-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Search Return No">
        </div>

        <div class="col-md-3">
            <input type="date" name="date" value="{{ request('date') }}"
                   class="form-control">
        </div>

        <div class="col-md-3">
            <button class="btn btn-primary">
                <i class="fa fa-search"></i> Filter
            </button>

            <a href="{{ route('purchase-return.index') }}" class="btn btn-secondary">
                Reset
            </a>
        </div>

    </form>
    

    <div class="card shadow-sm">

        <div class="table-responsive">

            <table class="table table-bordered table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Return No</th>
                        <th>Supplier</th>
                        <th>Invoice</th>
                        <th>Total Amount</th>
                        <th>Date</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($returns as $index => $return)

                        <tr>

                            <td>
                                {{ $returns->firstItem() + $index }}
                            </td>

                            <td>
                                <strong class="text-primary">
                                    {{ $return->return_number }}
                                </strong>
                            </td>

                            <td>
                                {{ $return->supplier->name ?? '-' }}
                            </td>

                            <td>
                                {{ $return->purchase->invoice_number ?? '-' }}
                            </td>

                            <td>
                                <span class="fw-bold text-success">
                                    ₹ {{ number_format($return->total_amount, 2) }}
                                </span>
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($return->return_date)->format('d-m-Y') }}
                            </td>

                            <td>

                                <!-- DELETE -->
                                <form action="{{ route('purchase-return.delete', $return->id) }}"
                                      method="POST"
                                      style="display:inline-block"
                                      onsubmit="return confirm('Delete this return?')">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="text-center py-4">

                                <div class="text-muted">
                                    <i class="fa fa-box-open fa-2x mb-2"></i>
                                    <br>
                                    No Purchase Returns Found
                                </div>

                            </td>
                        </tr>

                    @endforelse
                </tbody>

            </table>

        </div>

        <!-- PAGINATION -->
        @if($returns->hasPages())
            <div class="p-3">
                {{ $returns->appends(request()->query())->links() }}
            </div>
        @endif

    </div>

</div>

@endsection