@extends('layouts.master')

@section('content')

<div class="container-fluid">

<h5>Create Purchase Order</h5>

<form method="POST" action="{{ route('purchase-orders.store') }}">
@csrf

<input type="date" name="order_date" class="form-control mb-3" required>

<table class="table" id="items_table">
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
<option value="{{ $item->id }}">{{ $item->name }}</option>
@endforeach
</select>

<input type="hidden" name="items[0][supplier_item_catalog_id]" class="catalog_id">
</td>

<td><input type="number" name="items[0][quantity]" class="form-control qty"></td>
<td><input type="number" name="items[0][rate]" class="form-control rate"></td>
<td><input type="number" name="items[0][gst_percent]" class="form-control gst"></td>
<td><input type="number" name="items[0][discount_percent]" class="form-control discount"></td>
<td><input type="text" class="form-control total" readonly></td>
<td><button type="button" class="btn btn-danger remove-row">X</button></td>
</tr>

<tr class="supplier-row" style="display:none;">
<td colspan="7"><div class="supplier-list"></div></td>
</tr>

</tbody>
</table>

<button type="button" id="add_row" class="btn btn-primary">+ Add Item</button>

<div class="text-end mt-3">
<button class="btn btn-success">Save</button>
</div>

</form>
</div>

@endsection

@push('scripts')
<script>

let rowIndex = 1;

// ADD ROW
document.getElementById('add_row').onclick = function(){

let row = `
<tr>
<td>
<select name="items[${rowIndex}][item_id]" class="form-control item-select">
<option value="">Select Item</option>
@foreach($items as $item)
<option value="{{ $item->id }}">{{ $item->name }}</option>
@endforeach
</select>

<input type="hidden" name="items[${rowIndex}][supplier_item_catalog_id]" class="catalog_id">
</td>

<td><input type="number" name="items[${rowIndex}][quantity]" class="form-control qty"></td>
<td><input type="number" name="items[${rowIndex}][rate]" class="form-control rate"></td>
<td><input type="number" name="items[${rowIndex}][gst_percent]" class="form-control gst"></td>
<td><input type="number" name="items[${rowIndex}][discount_percent]" class="form-control discount"></td>
<td><input type="text" class="form-control total" readonly></td>
<td><button type="button" class="btn btn-danger remove-row">X</button></td>
</tr>

<tr class="supplier-row" style="display:none;">
<td colspan="7"><div class="supplier-list"></div></td>
</tr>
`;

document.querySelector('#items_table tbody').insertAdjacentHTML('beforeend', row);
rowIndex++;
};

// ITEM SELECT
document.addEventListener('change', function(e){

if(e.target.classList.contains('item-select')){

let itemId = e.target.value;
let row = e.target.closest('tr');
let supplierRow = row.nextElementSibling;
let list = supplierRow.querySelector('.supplier-list');

fetch(`/item/suppliers?item_id=${itemId}`)
.then(res => res.json())
.then(data => {

let html = '';

data.forEach(item => {
html += `
<div class="supplier-card"
data-id="${item.id}"
data-rate="${item.purchase_price}"
data-gst="${item.gst_percent}"
style="border:1px solid #ccc; padding:10px; margin:5px; cursor:pointer;">
<b>${item.supplier.name}</b><br>
₹${item.purchase_price}
</div>
`;
});

list.innerHTML = html;
supplierRow.style.display = 'table-row';
});

}
});

// SELECT SUPPLIER
document.addEventListener('click', function(e){

if(e.target.classList.contains('supplier-card')){

let card = e.target;
let row = card.closest('tr').previousElementSibling;

row.querySelector('.rate').value = card.dataset.rate;
row.querySelector('.gst').value = card.dataset.gst;
row.querySelector('.catalog_id').value = card.dataset.id;

}
});

// TOTAL CALCULATION
document.addEventListener('input', function(e){

if(e.target.classList.contains('qty') || e.target.classList.contains('rate')){

let row = e.target.closest('tr');

let qty = parseFloat(row.querySelector('.qty').value) || 0;
let rate = parseFloat(row.querySelector('.rate').value) || 0;

row.querySelector('.total').value = (qty * rate).toFixed(2);

}
});

</script>
@endpush