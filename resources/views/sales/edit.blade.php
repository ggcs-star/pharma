@extends('layouts.master')

@section('content')

<div class="card">
    <div class="card-header bg-warning text-dark">
        <h5>Edit Sale - {{ $sale->bill_number }}</h5>
    </div>

    <div class="card-body">

        <form action="{{ route('sales.update',$sale->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-4">

                <div class="col-md-4">
                    <label>Bill Number</label>
                    <input type="text"
                           class="form-control"
                           value="{{ $sale->bill_number }}"
                           readonly>
                </div>

                <div class="col-md-4">
                    <label>Bill Date</label>
                    <input type="date"
                           name="bill_date"
                           value="{{ $sale->bill_date }}"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-4">
                    <label>Customer</label>
                    <select name="customer_id"
                            class="form-control">
                        <option value="">Walk-in</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}"
                                {{ $sale->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <table class="table table-bordered" id="editTable">
                <thead class="table-dark">
                    <tr>
                        <th>Medicine</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($sale->items as $index => $item)
                    <tr>
                        <td>
                            <select name="items[{{ $index }}][medicine_id]"
                                    class="form-control">
                                @foreach($medicines as $medicine)
                                    <option value="{{ $medicine->id }}"
                                        {{ $item->medicine_id == $medicine->id ? 'selected' : '' }}>
                                        {{ $medicine->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td>
                            <input type="number"
                                   name="items[{{ $index }}][quantity]"
                                   value="{{ $item->quantity }}"
                                   class="form-control">
                        </td>

                        <td>
                            <input type="number"
                                   step="0.01"
                                   name="items[{{ $index }}][sale_rate]"
                                   value="{{ $item->sale_rate }}"
                                   class="form-control">
                        </td>

                        <td>
                            ₹ {{ $item->amount }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-end">
                <h4>Total: ₹ {{ $sale->net_amount }}</h4>
            </div>

            <button class="btn btn-warning mt-3">
                Update Sale
            </button>

        </form>

    </div>
</div>

@endsection