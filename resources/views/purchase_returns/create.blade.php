@extends('layouts.master')

@section('content')

<div class="main-wrapper">

    <h4 class="mb-3">Purchase Return (Item Wise)</h4>

    <form method="POST" action="{{ route('purchase-return.store') }}" id="returnForm">
        @csrf

        <!-- 🔥 GLOBAL PURCHASE ID - REQUIRED BY CONTROLLER -->
        <div class="card mb-3 p-3">
            <div class="row">
                <div class="col-md-6">
                    <label for="purchase_id" class="form-label">Purchase Invoice <span class="text-danger">*</span></label>
                    <select name="purchase_id" id="purchase_id" class="form-control" required>
                        <option value="">Select Purchase Invoice</option>
                        @php
                            $uniquePurchases = [];
                        @endphp
                        @foreach($purchaseItems as $pi)
                            @if(!in_array($pi->purchase_id, $uniquePurchases))
                                @php
                                    $uniquePurchases[] = $pi->purchase_id;
                                @endphp
                                <option value="{{ $pi->purchase_id }}" 
                                    data-supplier="{{ $pi->purchase->supplier->name }}"
                                    data-invoice="{{ $pi->purchase->invoice_number }}">
                                    {{ $pi->purchase->invoice_number }} - 
                                    {{ $pi->purchase->supplier->name }} - 
                                    {{ \Carbon\Carbon::parse($pi->purchase->purchase_date)->format('d-m-Y') }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Selected Supplier</label>
                    <input type="text" id="selected_supplier" class="form-control" readonly disabled>
                </div>
            </div>
        </div>

        <div class="card p-3">

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th width="25%">Item</th>
                            <th>Batch</th>
                            <th>Supplier</th>
                            <th>Invoice</th>
                            <th>Available Stock</th>
                            <th width="120">Return Qty</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th width="60"></th>
                        </tr>
                    </thead>
                    <tbody id="rows">
                        <!-- Items will be auto-populated here -->
                    </tbody>
                </table>
            </div>

            <div class="text-end mt-3">
                <h5>Total: ₹ <span id="grandTotal">0</span></h5>
            </div>

            <button type="submit" class="btn btn-success mt-2" id="submitBtn">
                Submit Return
            </button>

        </div>

    </form>

</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let purchaseItemsData = {!! json_encode($purchaseItems->map(function($pi) {
        return [
            'id' => $pi->id,
            'purchase_id' => $pi->purchase_id,
            'item_id' => $pi->item_id,
            'batch_id' => $pi->batch_id,
            'item_name' => $pi->item->name,
            'batch_code' => $pi->batch->batch_code,
            'supplier_name' => $pi->purchase->supplier->name,
            'invoice_number' => $pi->purchase->invoice_number,
            'stock' => $pi->batch->stock,
            'rate' => $pi->ptr,
            'purchased_qty' => $pi->quantity,
            'returned_qty' => $pi->returned_quantity ?? 0,
    'available_qty' => max(0, $pi->batch->stock),
        ];
    })) !!};
    
    let currentRows = [];
    let rowCounter = 0;

    // Load items when purchase is selected
    $('#purchase_id').on('change', function() {
        let purchaseId = $(this).val();
        
        if (!purchaseId) {
            $('#selected_supplier').val('');
            $('#rows').empty();
            calculateTotal();
            return;
        }
        
        // Update supplier info
        let supplier = $(this).find('option:selected').data('supplier');
        let invoice = $(this).find('option:selected').data('invoice');
        $('#selected_supplier').val(supplier);
        
        // Filter items for selected purchase
        let filteredItems = purchaseItemsData.filter(item => 
            item.purchase_id == purchaseId && 
            item.available_qty > 0
        );
        
        // Populate rows with available items
        populateItems(filteredItems);
    });
    
    function populateItems(items) {
        $('#rows').empty();
        currentRows = [];
        rowCounter = 0;
        
        if (items.length === 0) {
            $('#rows').html('<tr><td colspan="9" class="text-center">No items available for return</td></tr>');
            calculateTotal();
            return;
        }
        
        items.forEach((item, index) => {
            addItemRow(item, index);
        });
        
        calculateTotal();
    }
    
    function addItemRow(item, index) {
        let rowId = rowCounter++;
        currentRows.push({
            id: rowId,
            item_id: item.item_id,
            batch_id: item.batch_id,
            purchase_item_id: item.id,
            available_qty: item.available_qty,
            rate: item.rate,
            quantity: 0
        });
        
        let rowHtml = `
            <tr data-row-id="${rowId}">
                <td>
                    <strong>${item.item_name}</strong>
                    <input type="hidden" name="items[${index}][purchase_item_id]" value="${item.id}" class="purchase_item_id">
                    <input type="hidden" name="items[${index}][item_id]" value="${item.item_id}" class="item_id">
                    <input type="hidden" name="items[${index}][batch_id]" value="${item.batch_id}" class="batch_id">
                </td>
                <td class="batch">${item.batch_code}</td>
                <td class="supplier">${item.supplier_name}</td>
                <td class="invoice">${item.invoice_number}</td>
                <td class="stock" data-max="${item.available_qty}">${item.available_qty}</td>
                <td>
                    <input type="number" step="0.01" min="0" max="${item.available_qty}"
                           name="items[${index}][quantity]"
                           class="form-control qty"
                           data-row-id="${rowId}">
                </td>
                <td class="rate">${parseFloat(item.rate).toFixed(2)}</td>
                <td class="amount">0.00</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row" data-row-id="${rowId}">X</button>
                </td>
            </tr>
        `;
        
        $('#rows').append(rowHtml);
    }
    
    // Add new row button (for any additional items that might be needed)
    $('#addRow').off('click').on('click', function() {
        let purchaseId = $('#purchase_id').val();
        
        if (!purchaseId) {
            alert('Please select a purchase invoice first');
            return;
        }
        
        // Check if there are any items left to add
        let usedItemIds = [];
        $('.purchase_item_id').each(function() {
            usedItemIds.push($(this).val());
        });
        
        let availableItems = purchaseItemsData.filter(item => 
            item.purchase_id == purchaseId && 
            item.available_qty > 0 &&
            !usedItemIds.includes(item.id.toString())
        );
        
        if (availableItems.length === 0) {
            alert('No more items available to add');
            return;
        }
        
        // Create a modal or dropdown to select which item to add
        let itemOptions = availableItems.map((item, idx) => 
            `<option value="${idx}">${item.item_name} (Available: ${item.available_qty})</option>`
        ).join('');
        
        let selectedIndex = prompt("Select item to add:\n" + 
            availableItems.map((item, idx) => `${idx + 1}. ${item.item_name} (Available: ${item.available_qty})`).join('\n'));
        
        if (selectedIndex && selectedIndex > 0 && selectedIndex <= availableItems.length) {
            let selectedItem = availableItems[selectedIndex - 1];
            let currentRowCount = $('.purchase_item_id').length;
            addItemRow(selectedItem, currentRowCount);
            reindexRows();
            calculateTotal();
        }
    });
    
    function reindexRows() {
        $('.purchase_item_id').each(function(index) {
            let name = $(this).attr('name');
            $(this).attr('name', `items[${index}][purchase_item_id]`);
            $(this).closest('tr').find('.item_id').attr('name', `items[${index}][item_id]`);
            $(this).closest('tr').find('.batch_id').attr('name', `items[${index}][batch_id]`);
            $(this).closest('tr').find('.qty').attr('name', `items[${index}][quantity]`);
        });
    }
    
    // Quantity input handler
    $(document).on('input', '.qty', function() {
        let $row = $(this).closest('tr');
        let $stockElem = $row.find('.stock');
        let $rateElem = $row.find('.rate');
        
        let qty = parseFloat($(this).val()) || 0;
        let stock = parseFloat($stockElem.text());
        let rate = parseFloat($rateElem.text());
        
        // Validation
        if (qty < 0) {
            alert("Quantity cannot be negative");
            $(this).val('');
            return;
        }
        
        if (qty > stock) {
            alert(`Cannot return more than available stock (${stock})`);
            $(this).val('');
            $row.find('.amount').text('0.00');
            calculateTotal();
            return;
        }
        
        // Calculate amount
        let amount = qty * rate;
        $row.find('.amount').text(amount.toFixed(2));
        
        calculateTotal();
    });
    
    // Remove row handler
    $(document).on('click', '.remove-row', function() {
        let rowId = $(this).data('row-id');
        let $row = $(this).closest('tr');
        
        // Check if this is the last row
        if ($('#rows tr').length === 1) {
            alert("At least one item is required");
            return;
        }
        
        $row.remove();
        reindexRows();
        calculateTotal();
    });
    
    // Total calculation
    function calculateTotal() {
        let total = 0;
        
        $('.amount').each(function() {
            let amount = parseFloat($(this).text());
            if (!isNaN(amount)) {
                total += amount;
            }
        });
        
        $('#grandTotal').text(total.toFixed(2));
    }
    
    // Form submission validation
    $('#returnForm').on('submit', function(e) {
        let purchaseId = $('#purchase_id').val();
        let errors = [];
        
        if (!purchaseId) {
            errors.push("Please select a purchase invoice");
        }
        
        let hasValidItems = false;
        
        $('#rows tr').each(function(index) {
            let $row = $(this);
            let $qtyInput = $row.find('.qty');
            let qty = parseFloat($qtyInput.val()) || 0;
            let itemName = $row.find('td:first strong').text();
            
            if (qty > 0) {
                hasValidItems = true;
            } else if ($row.find('.purchase_item_id').val() && qty <= 0) {
                errors.push(`Row ${index + 1}: Please enter valid quantity for ${itemName}`);
            }
        });
        
        if (!hasValidItems && errors.length === 0) {
            errors.push("Please add at least one valid return item");
        }
        
        if (errors.length > 0) {
            e.preventDefault();
            alert("Please fix the following errors:\n" + errors.join('\n'));
            return false;
        }
        
        // Confirm submission
        let totalAmount = parseFloat($('#grandTotal').text());
        if (totalAmount > 0) {
            return confirm(`Total return amount: ₹${totalAmount.toFixed(2)}\nDo you want to proceed?`);
        }
        
        return true;
    });
    
    // Initialize total on page load
    calculateTotal();
});
</script>
@endpush