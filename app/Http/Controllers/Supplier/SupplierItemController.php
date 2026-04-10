<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Item;

class SupplierItemController extends Controller
{

    public function index()
{
    $supplierId = auth('supplier')->id();

    $items = Item::with(['category','subCategory','manufacturer'])
    ->whereHas('catalogs', function ($q) use ($supplierId) {
        $q->where('supplier_id', $supplierId);
    })
    ->latest()
    ->paginate(10);

    return view('supplier.item.index', compact('items'));
}

   
   public function show($id)
{
    $supplierId = auth('supplier')->id();

    $item = Item::with([
        'category',
        'subCategory',
        'manufacturer',
        'packType',
        'unit',
        'catalogs' => function ($q) use ($supplierId) {
            $q->where('supplier_id', $supplierId)->with('stocks');
        }
    ])->findOrFail($id);
// dd($item->catalogs);
    return view('supplier.item.show', compact('item'));
}

}