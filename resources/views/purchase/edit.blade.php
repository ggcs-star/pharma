@extends('layouts.master')

@section('content')
<div class="container-fluid px-4 py-3">
<div class="card shadow-lg border-0 rounded-4 overflow-hidden">

{{-- HEADER --}}
<div class="card-header bg-gradient-primary text-white py-4 border-0">
    <h4 class="mb-0 fw-bold">
        <i class="fas fa-edit me-2"></i> Edit Purchase
    </h4>
</div>

<div class="card-body p-4">

<form action="{{ route('purchase.update',$purchase->id) }}" method="POST" id="purchaseForm">
@csrf
@method('PUT')

{{-- HEADER FIELDS --}}
<div class="row g-4 mb-4">

    <div class="col-md-3">
        <label>Invoice No</label>
        <input type="text" name="invoice_number"
        value="{{ $purchase->invoice_number }}" class="form-control">
    </div>

    <div class="col-md-3">
        <label>Date</label>
        <!-- <input type="date" name="purchase_date" -->
<input type="date" name="purchase_date"
value="{{ $purchase->purchase_date 
    ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') 
    : $purchase->created_at->format('Y-m-d') }}"
class="form-control">    </div>

    <div class="col-md-4">
        <label>Supplier</label>
        <select name="supplier_id" class="form-control">
            @foreach($suppliers as $s)
                <option value="{{ $s->id }}"
                {{ $purchase->supplier_id == $s->id ? 'selected':'' }}>
                {{ $s->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label>Payment</label>
        <select name="payment_type" class="form-control">
            <option value="Pending" {{ $purchase->payment_type=='Pending'?'selected':'' }}>Pending</option>
            <option value="Cash" {{ $purchase->payment_type=='Cash'?'selected':'' }}>Cash</option>
            <option value="UPI" {{ $purchase->payment_type=='UPI'?'selected':'' }}>UPI</option>
            <option value="Bank" {{ $purchase->payment_type=='Bank'?'selected':'' }}>Bank</option>
        </select>
    </div>

</div>

{{-- ITEMS --}}
<div class="table-responsive">
<table class="table table-bordered" id="purchaseTable">
<thead class="table-dark">
<tr>
<th>#</th>
<th>Item</th>
<th>Qty</th>
<th>Free</th>
<th>Batch</th>
<th>Expiry</th>
<th>MRP</th>
<th>PTR</th>
<th>GST%</th>
<th>Disc%</th>
<th>Disc ₹</th>
<th>Taxable</th>
<th>GST</th>
<th>Total</th>
<th></th>
</tr>
</thead>

<tbody></tbody>
</table>
</div>

<button type="button" class="btn btn-primary mt-3" onclick="addRow()">+ Add Item</button>

{{-- TOTAL --}}
<div class="row justify-content-end mt-4">
<div class="col-md-4">

<div class="mb-2">
<label>Extra</label>
<input type="number" name="extra_charges" id="extraCharges"
value="{{ $purchase->extra_charges }}" class="form-control">
</div>

<div class="mb-2">
<label>Round</label>
<input type="number" name="round_off" id="roundOff"
value="{{ $purchase->round_off }}" class="form-control">
</div>

<h4>Total: ₹ <span id="grandTotal">0.00</span></h4>

</div>
</div>

<button class="btn btn-success mt-3">Update Purchase</button>

</form>
</div>
</div>
</div>

{{-- TEMPLATE --}}
<div style="display:none">
<select id="itemTemplate">
@foreach($items as $item)
<option value="{{ $item->id }}">{{ $item->name }}</option>
@endforeach
</select>
</div>

<script>
let rowIndex = 0;

function createRow(index){
return `
<tr>
<td>${index+1}</td>

<td>
<select name="items[${index}][item_id]" class="itemSelect form-control">
<option value="">Select</option>
${document.getElementById('itemTemplate').innerHTML}
</select>
</td>

<td><input type="number" name="items[${index}][quantity]" class="qty form-control" value="0"></td>
<td><input type="number" name="items[${index}][free_quantity]" class="freeQty form-control" value="0"></td>
<td><input type="text" name="items[${index}][batch_number]" class="batchNumber form-control"></td>
<td><input type="date" name="items[${index}][expiry_date]" class="expiryDate form-control"></td>
<td><input type="number" name="items[${index}][mrp]" class="mrp form-control"></td>
<td><input type="number" name="items[${index}][purchase_rate]" class="rate form-control"></td>
<td><input type="number" name="items[${index}][gst_percent]" class="gst form-control" value="0"></td>
<td><input type="number" name="items[${index}][discount_percent]" class="discP form-control" value="0"></td>
<td><input type="number" name="items[${index}][discount_amount]" class="discA form-control" value="0"></td>

<td><input class="taxable form-control" readonly></td>
<td><input class="gstAmt form-control" readonly></td>
<td><input class="amount form-control" readonly></td>

<td><button onclick="removeRow(this)" type="button">X</button></td>
</tr>`;
}

function addRow(){
let tbody = document.querySelector("#purchaseTable tbody");
tbody.insertAdjacentHTML('beforeend', createRow(rowIndex));
rowIndex++;
}

function removeRow(btn){
btn.closest('tr').remove();
calculateTotal();
}

function calculateRow(row){
let qty = parseFloat(row.querySelector('.qty').value)||0;
let rate = parseFloat(row.querySelector('.rate').value)||0;
let gst = parseFloat(row.querySelector('.gst').value)||0;

let basic = qty*rate;
let gstAmt = basic*gst/100;
let total = basic+gstAmt;

row.querySelector('.taxable').value = basic.toFixed(2);
row.querySelector('.gstAmt').value = gstAmt.toFixed(2);
row.querySelector('.amount').value = total.toFixed(2);
}

function calculateTotal(){
let total=0;
document.querySelectorAll('.amount').forEach(el=>{
total+=parseFloat(el.value)||0;
});
document.getElementById('grandTotal').innerText=total.toFixed(2);
}

document.addEventListener('input',function(e){
let row=e.target.closest('tr');
if(row){
calculateRow(row);
calculateTotal();
}
});

{{-- 🔥 PREFILL DATA --}}
document.addEventListener('DOMContentLoaded',function(){

let items=@json($purchase->items);

items.forEach((item,index)=>{
addRow();
let row=document.querySelectorAll("#purchaseTable tbody tr")[index];

row.querySelector('.itemSelect').value=item.item_id;
row.querySelector('.qty').value=item.quantity;
row.querySelector('.freeQty').value=item.free_quantity;
// SAFE batch
row.querySelector('.batchNumber').value = item.batch?.batch_code ?? '';

// FIX expiry format
let expiry = item.batch?.expiry_date ?? '';

if(expiry){
    expiry = expiry.split('T')[0]; // 🔥 MAIN FIX
}

row.querySelector('.expiryDate').value = expiry;
row.querySelector('.mrp').value=item.mrp;
row.querySelector('.rate').value=item.ptr;
row.querySelector('.gst').value=item.gst_percent;
row.querySelector('.discP').value=item.discount_percent;
row.querySelector('.discA').value=item.discount_amount;

calculateRow(row);
});

calculateTotal();

});
</script>

@endsection