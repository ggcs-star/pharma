<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\SupplierStock;
use App\Models\SupplierItemCatalog;
use Illuminate\Http\Request;

class SupplierStockController extends Controller
{
    public function index()
    {
        $stocks = SupplierStock::with('catalog.item')
            ->latest()
            ->get();

        return view('supplier.stocks.index', compact('stocks'));
    }

    public function create()
    {
        $catalogs = SupplierItemCatalog::with('item')->get();
        return view('supplier.stocks.create', compact('catalogs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_item_catalog_id' => 'required',
            'qty' => 'required|integer',
            'type' => 'required|in:purchase,sale,return,adjustment',
        ]);

        // ❗ Sale me negative qty kar do
        $qty = $request->type === 'sale'
            ? -abs($request->qty)
            : abs($request->qty);

        SupplierStock::create([
            'supplier_item_catalog_id' => $request->supplier_item_catalog_id,
            'qty' => $qty,
            'type' => $request->type,
            'note' => $request->note,
        ]);

        return redirect()->route('supplier.stocks.index')->with('success', 'Stock updated');
    }

    public function destroy($id)
    {
        $stock = SupplierStock::findOrFail($id);
        $stock->delete();

        return back()->with('success', 'Deleted');
    }
}