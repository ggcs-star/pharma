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
<th>PTR (Rate)</th>
<th>MRP</th>
<th>GST%</th>
<th>Total</th>
<th></th>
</tr>
</thead>

<tbody>

<tr>
<td>
<select name="items[0][item_id]" class="form-control item-select" placeholder="Search or select item...">
<option value="">Select Item</option>
@foreach($items as $item)
<option value="{{ $item->id }}">{{ $item->name }}</option>
@endforeach
</select>

<input type="hidden" name="items[0][supplier_item_catalog_id]" class="catalog_id">
</td>

<td><input type="number" name="items[0][quantity]" class="form-control qty" value="1"></td>
<td><input type="number" name="items[0][rate]" class="form-control rate"></td>
<td><input type="number" class="form-control mrp" readonly></td>
<td><input type="number" name="items[0][gst_percent]" class="form-control gst"></td>
<td><input type="text" class="form-control total" readonly></td>
<td><button type="button" class="btn btn-danger remove-row">X</button></td>
</tr>

<tr class="supplier-row" style="display:none;">
<td colspan="7"><div class="supplier-list d-flex flex-wrap"></div></td>
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

@push('styles')
<!-- TomSelect CSS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">

<style>
/* Fix TomSelect dropdown z-index inside table */
.ts-dropdown {
    z-index: 1050 !important;
    position: absolute !important;
}

/* Ensure TomSelect doesn't overflow table cell */
.ts-wrapper {
    min-width: 200px;
}

/* Smooth styling for supplier cards */
.supplier-card {
    transition: all 0.2s ease;
}

.supplier-card:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Ensure proper table cell alignment */
#items_table td {
    vertical-align: middle;
}
</style>
@endpush

@push('scripts')
<!-- TomSelect JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
(function() {
    'use strict';

    // Store TomSelect instances
    const tomSelectInstances = new Map();
    let rowIndex = 1;

    // TomSelect configuration
    const tomSelectConfig = {
        valueField: 'value',
        labelField: 'text',
        searchField: ['text'],
        placeholder: 'Search or select item...',
        allowEmptyOption: true,
        create: false,
        maxOptions: null,
        render: {
            option: function(item, escape) {
                return `<div>${escape(item.text)}</div>`;
            }
        }
    };

    // Initialize TomSelect on a select element
    function initTomSelect(selectElement) {
        if (!selectElement) return null;
        
        const id = selectElement.name || `ts-${Date.now()}-${Math.random()}`;
        
        // Destroy existing instance if any
        if (tomSelectInstances.has(id)) {
            tomSelectInstances.get(id).destroy();
            tomSelectInstances.delete(id);
        }
        
        // Create new instance
        const instance = new TomSelect(selectElement, tomSelectConfig);
        tomSelectInstances.set(id, instance);
        
        return instance;
    }

    // Initialize all item selects
    function initAllTomSelects() {
        document.querySelectorAll('.item-select').forEach(select => {
            // Skip if already initialized
            if (select.tomselect) return;
            initTomSelect(select);
        });
    }

    // Clean up TomSelect instance for a row
    function destroyTomSelectForRow(row) {
        const select = row.querySelector('.item-select');
        if (select && select.tomselect) {
            const id = select.name;
            if (tomSelectInstances.has(id)) {
                tomSelectInstances.get(id).destroy();
                tomSelectInstances.delete(id);
            }
        }
    }

    // ADD ROW
    document.getElementById('add_row').onclick = function() {
        let newRowHtml = `
        <tr>
            <td>
                <select name="items[${rowIndex}][item_id]" class="form-control item-select" placeholder="Search or select item...">
                    <option value="">Select Item</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="items[${rowIndex}][supplier_item_catalog_id]" class="catalog_id">
            </td>
            <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control qty" value="1"></td>
            <td><input type="number" name="items[${rowIndex}][rate]" class="form-control rate"></td>
            <td><input type="number" class="form-control mrp" readonly></td>
            <td><input type="number" name="items[${rowIndex}][gst_percent]" class="form-control gst"></td>
            <td><input type="text" class="form-control total" readonly></td>
            <td><button type="button" class="btn btn-danger remove-row">X</button></td>
        </tr>
        <tr class="supplier-row" style="display:none;">
            <td colspan="7"><div class="supplier-list d-flex flex-wrap"></div></td>
        </tr>
        `;
        
        document.querySelector('#items_table tbody').insertAdjacentHTML('beforeend', newRowHtml);
        
        // Initialize TomSelect on new row
        setTimeout(() => {
            const newRow = document.querySelector(`select[name="items[${rowIndex}][item_id]"]`)?.closest('tr');
            if (newRow) {
                const select = newRow.querySelector('.item-select');
                if (select) initTomSelect(select);
            }
        }, 10);
        
        rowIndex++;
    };

    // ITEM SELECT - Trigger supplier fetch
    document.addEventListener('change', function(e) {
        if (!e.target.classList.contains('item-select')) return;
        
        const itemId = e.target.value;
        if (!itemId) return;
        
        const row = e.target.closest('tr');
        const supplierRow = row.nextElementSibling;
        
        if (!supplierRow || !supplierRow.classList.contains('supplier-row')) return;
        
        const list = supplierRow.querySelector('.supplier-list');
        if (!list) return;
        
        // Show loading state
        list.innerHTML = '<div class="p-3">Loading suppliers...</div>';
        supplierRow.style.display = 'table-row';
        
        fetch(`/item/suppliers?item_id=${itemId}`)
            .then(res => res.json())
            .then(data => {
                if (!data.length) {
                    list.innerHTML = '<div class="p-3 text-muted">No suppliers found for this item</div>';
                    return;
                }
                
                let html = '';
                data.forEach(item => {
                    html += `
                    <div class="supplier-card"
                         data-id="${item.id}"
                         data-rate="${item.retailer_price}"
                         data-mrp="${item.base_price}"
                         data-gst="${item.gst_percent}"
                         style="border:1px solid #ccc; padding:10px; margin:5px; cursor:pointer; border-radius:6px;">
                        <b>${item.supplier.name}</b><br>
                        PTR: ₹${item.retailer_price}<br>
                        MRP: ₹${item.base_price}
                    </div>
                    `;
                });
                
                list.innerHTML = html;
            })
            .catch(err => {
                console.error('Error fetching suppliers:', err);
                list.innerHTML = '<div class="p-3 text-danger">Error loading suppliers</div>';
            });
    });

    // SELECT SUPPLIER
    document.addEventListener('click', function(e) {
        const card = e.target.closest('.supplier-card');
        if (!card) return;
        
        const supplierRow = card.closest('tr');
        const itemRow = supplierRow.previousElementSibling;
        
        if (!itemRow) return;
        
        // Fill item row with supplier data
        itemRow.querySelector('.rate').value = card.dataset.rate;
        itemRow.querySelector('.mrp').value = card.dataset.mrp;
        itemRow.querySelector('.gst').value = card.dataset.gst;
        itemRow.querySelector('.catalog_id').value = card.dataset.id;
        
        // Hide supplier row
        supplierRow.style.display = 'none';
        
        // Trigger total calculation if quantity exists
        const qtyInput = itemRow.querySelector('.qty');
        if (qtyInput && qtyInput.value) {
            calculateTotal(itemRow);
        }
    });

    // TOTAL CALCULATION
    function calculateTotal(row) {
        const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
        const rate = parseFloat(row.querySelector('.rate')?.value) || 0;
        const totalInput = row.querySelector('.total');
        if (totalInput) {
            totalInput.value = (qty * rate).toFixed(2);
        }
    }

    document.addEventListener('input', function(e) {
        if (!e.target.classList.contains('qty') && !e.target.classList.contains('rate')) return;
        const row = e.target.closest('tr');
        if (row) calculateTotal(row);
    });

    // REMOVE ROW
    document.addEventListener('click', function(e) {
        if (!e.target.classList.contains('remove-row')) return;
        
        const row = e.target.closest('tr');
        const nextRow = row.nextElementSibling;
        
        // Clean up TomSelect instance
        destroyTomSelectForRow(row);
        
        // Remove rows
        row.remove();
        if (nextRow && nextRow.classList.contains('supplier-row')) {
            nextRow.remove();
        }
    });

    // Initialize TomSelect on page load
    document.addEventListener('DOMContentLoaded', function() {
        initAllTomSelects();
    });

})();
</script>
@endpush