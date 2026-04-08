<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\SupplierItemCatalog;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\SupplierStock;
use Illuminate\Support\Facades\DB;
class SupplierItemCatalogController extends Controller
{
    public function index()
    {
        $catalogs = SupplierItemCatalog::with('item')
            ->where('supplier_id', auth('supplier')->id())
            ->latest()
            ->get();

        return view('supplier.catalogs.index', compact('catalogs'));
    }

    public function create()
    {
        $items = Item::all();
        return view('supplier.catalogs.create', compact('items'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'purchase_price' => 'required|numeric',
            'retailer_price' => 'required|numeric',
            'retailer_mrp' => 'required|numeric',
            'qty' => 'nullable|integer'
        ]);

        DB::beginTransaction();

        try {

            $data = $request->all();
            $data['supplier_id'] = auth('supplier')->id();

            // 🔥 auto base price
            $data['base_price'] = $request->retailer_price;

            // 🔥 initial stock
            $initialQty = $request->qty ?? 0;
            $data['current_stock'] = $initialQty;

            // ✅ Step 1: create catalog
            $catalog = SupplierItemCatalog::create($data);

            // ✅ Step 2: create stock entry (if qty given)
            if ($initialQty > 0) {
                SupplierStock::create([
                    'supplier_item_catalog_id' => $catalog->id,
                    'qty' => $initialQty,
                    'type' => 'purchase',
                    'note' => 'Initial stock added'
                ]);
            }

            DB::commit();

            return redirect()->route('supplier.catalogs.index')->with('success', 'Catalog + Stock created');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $catalog = SupplierItemCatalog::where('supplier_id', auth('supplier')->id())->findOrFail($id);
        $items = Item::all();

        return view('supplier.catalogs.edit', compact('catalog', 'items'));
    }

    public function update(Request $request, $id)
    {
        $catalog = SupplierItemCatalog::where('supplier_id', auth('supplier')->id())->findOrFail($id);

        $request->validate([
            'purchase_price' => 'required|numeric',
            'retailer_price' => 'required|numeric',
            'retailer_mrp' => 'required|numeric',
        ]);

        $catalog->update($request->all());

        return redirect()->route('supplier.catalogs.index')->with('success', 'Updated successfully');
    }

    public function destroy($id)
    {
        $catalog = SupplierItemCatalog::where('supplier_id', auth('supplier')->id())->findOrFail($id);
        $catalog->delete();

        return back()->with('success', 'Deleted successfully');
    }
}
