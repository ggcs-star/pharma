@extends('layouts.master')

@section('content')

<div class="card shadow-sm">

    <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
        <h5 class="mb-0">Suppliers</h5>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm">
            + Add Supplier
        </a>
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-striped align-middle">

                <thead class="table-dark text-center">
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Supplier</th>
                        <th>GSTIN</th>
                        <th>Phone</th>
                        <th>Drug License</th>
                        <th>T. Balance</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($suppliers as $index => $supplier)

                    @php
                        $balance = $supplier->ledgers->sum('debit') - $supplier->ledgers->sum('credit');
                    @endphp

                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td>{{ $supplier->supplier_code ?? '-' }}</td>

                        <td>
                            <strong>{{ $supplier->name }}</strong>
                        </td>

                        <td>{{ $supplier->gst_in ?? '-' }}</td>

                        <td>{{ $supplier->phone ?? '-' }}</td>

                        <td>{{ $supplier->drug_license ?? '-' }}</td>

                        {{-- 🔥 BALANCE COLOR --}}
                        <td class="text-end">
                            @if($balance > 0)
                                <span class="text-danger fw-bold">
                                    ₹ {{ number_format($balance,2) }}
                                </span>
                            @else
                                <span class="text-success fw-bold">
                                    ₹ {{ number_format($balance,2) }}
                                </span>
                            @endif
                        </td>

                        <td class="text-center">

                            <a href="{{ route('suppliers.edit',$supplier->id) }}"
                               class="btn btn-warning btn-sm">✏️</a>

                            <form action="{{ route('suppliers.destroy',$supplier->id) }}"
                                  method="POST"
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')

                                <!-- <button class="btn btn-danger btn-sm">
                                    🗑
                                </button> -->
                            </form>

                        </td>
                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

        {{ $suppliers->links() }}

    </div>

</div>

@endsection