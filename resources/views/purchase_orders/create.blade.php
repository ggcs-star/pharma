@extends('layouts.master')

@section('content')

<div class="container-fluid">

<h5 class="mb-3">Create Purchase Order</h5>

<form method="POST" action="{{ route('purchase-orders.store') }}">
@csrf

<!-- SUPPLIER -->
<div class="row mb-3">

    <div class="col-md-6">
        <label>Supplier</label>
        <select name="supplier_id" class="form-control" required>
            <option value="">Select Supplier</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}">
                    {{ $supplier->supplier_code }} - {{ $supplier->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label>Date</label>
        <input type="date" name="order_date" class="form-control" required>
    </div>

</div>

<!-- ITEMS TABLE -->
<div class="card">
<div class="card-body p-0">

<table class="table table-bordered" id="items_table">
<thead>
<tr>
    <th>Item</th>
    <th>Qty</th>
    <th>Rate</th>
    <th>GST%</th>
    <th>Disc%</th>
    <th>Total</th>
    <th></th>
</tr>
</thead>

<tbody>

<tr>
    <td>
        <select name="items[0][item_id]" class="form-control item-select">
            <option value="">Select Item</option>
            @foreach($items as $item)
                <option value="{{ $item->id }}" 
                        data-gst="{{ $item->gst_percent }}"
                        data-rate="{{ $item->purchase_rate ?? 0 }}">
                    {{ $item->name }}
                </option>
            @endforeach
        </select>
    </td>

    <td><input type="number" name="items[0][quantity]" class="form-control qty"></td>
    <td><input type="number" name="items[0][rate]" class="form-control rate"></td>
    <td><input type="number" name="items[0][gst_percent]" class="form-control gst"></td>
    <td><input type="number" name="items[0][discount_percent]" class="form-control discount"></td>
    <td><input type="text" class="form-control total" readonly></td>

    <td><button type="button" class="btn btn-danger remove-row">X</button></td>
</tr>

</tbody>
</table>

</div>
</div>

<button type="button" id="add_row" class="btn btn-primary mt-2">+ Add Item</button>

<!-- 🔥 SUMMARY -->
<div class="row mt-3">

    <div class="col-md-4 offset-md-8">

        <div class="card p-3">

            <div class="d-flex justify-content-between">
                <span>Subtotal</span>
                <span id="subtotal">0.00</span>
            </div>

            <div class="d-flex justify-content-between">
                <span>Total GST</span>
                <span id="total_gst">0.00</span>
            </div>

            <div class="d-flex justify-content-between">
                <span>Total Discount</span>
                <span id="total_discount">0.00</span>
            </div>

            <hr>

            <div class="d-flex justify-content-between fw-bold">
                <span>Grand Total</span>
                <span id="grand_total">0.00</span>
            </div>

        </div>

    </div>

</div>

<div class="mt-3 text-end">
    <button class="btn btn-success">Save Purchase Order</button>
</div>

</form>

</div>

@endsection

@push('scripts')
<script>

// ADD ROW
let rowIndex = 1;

document.getElementById('add_row').onclick = function(){

    let row = `
    <tr>
        <td>
            <select name="items[${rowIndex}][item_id]" class="form-control item-select">
                @foreach($items as $item)
                    <option value="{{ $item->id }}" 
                            data-gst="{{ $item->gst_percent }}"
                            data-rate="{{ $item->purchase_rate ?? 0 }}">
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </td>

        <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control qty"></td>
        <td><input type="number" name="items[${rowIndex}][rate]" class="form-control rate"></td>
        <td><input type="number" name="items[${rowIndex}][gst_percent]" class="form-control gst"></td>
        <td><input type="number" name="items[${rowIndex}][discount_percent]" class="form-control discount"></td>
        <td><input type="text" class="form-control total" readonly></td>
        <td><button type="button" class="btn btn-danger remove-row">X</button></td>
    </tr>`;

    document.querySelector('#items_table tbody').insertAdjacentHTML('beforeend', row);
    rowIndex++;
};

// REMOVE ROW
document.addEventListener('click', function(e){
    if(e.target.classList.contains('remove-row')){
        e.target.closest('tr').remove();
        updateSummary();
    }
});

// AUTO GST + RATE
document.addEventListener('change', function(e){
    if(e.target.classList.contains('item-select')){
        let row = e.target.closest('tr');
        let option = e.target.selectedOptions[0];

        row.querySelector('.gst').value = option.dataset.gst || 0;
        row.querySelector('.rate').value = option.dataset.rate || 0;
    }
});

// CALCULATION
document.addEventListener('input', function(e){

    if (!e.target.closest('tr')) return;

    let row = e.target.closest('tr');

    let qty = parseFloat(row.querySelector('.qty').value) || 0;
    let rate = parseFloat(row.querySelector('.rate').value) || 0;
    let gst = parseFloat(row.querySelector('.gst').value) || 0;
    let disc = parseFloat(row.querySelector('.discount').value) || 0;

    let basic = qty * rate;
    let discount = (basic * disc) / 100;
    let taxable = basic - discount;
    let gstAmount = (taxable * gst) / 100;
    let total = taxable + gstAmount;

    row.querySelector('.total').value = total.toFixed(2);

    updateSummary();
});

// SUMMARY
function updateSummary(){

    let subtotal = 0;
    let totalGST = 0;
    let totalDiscount = 0;

    document.querySelectorAll('#items_table tbody tr').forEach(row => {

        let qty = parseFloat(row.querySelector('.qty').value) || 0;
        let rate = parseFloat(row.querySelector('.rate').value) || 0;
        let gst = parseFloat(row.querySelector('.gst').value) || 0;
        let disc = parseFloat(row.querySelector('.discount').value) || 0;

        let basic = qty * rate;
        let discount = (basic * disc) / 100;
        let taxable = basic - discount;
        let gstAmount = (taxable * gst) / 100;

        subtotal += basic;
        totalDiscount += discount;
        totalGST += gstAmount;

    });

    let grand = subtotal - totalDiscount + totalGST;

    document.getElementById('subtotal').innerText = subtotal.toFixed(2);
    document.getElementById('total_gst').innerText = totalGST.toFixed(2);
    document.getElementById('total_discount').innerText = totalDiscount.toFixed(2);
    document.getElementById('grand_total').innerText = grand.toFixed(2);
}

</script>
@endpush