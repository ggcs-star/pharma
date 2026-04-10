<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\SupplierItemCatalog;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\SupplierStock;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;
class SupplierItemCatalogController extends Controller
{
    public function index()
    {
        $catalogs = SupplierItemCatalog::with('item')
            ->where('supplier_id', auth('supplier')->id())
            ->latest()
            ->get();
// dd($catalogs);  
        return view('supplier.catalogs.index', compact('catalogs'));
    }
public function show($id)
{
    $catalog = SupplierItemCatalog::with(['item','stocks'])
        ->where('supplier_id', auth('supplier')->id())
        ->findOrFail($id);

    return view('supplier.catalogs.view', compact('catalog'));
}

    public function create()
    {
        $items = Item::with(['packType', 'unit'])->get();

        return view('supplier.catalogs.create', compact('items'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'batch_no' => 'required|unique:supplier_item_catalogs,batch_no',
            'base_price' => 'required|numeric',
            'purchase_price' => 'required|numeric',
            'retailer_price' => 'required|numeric',
            'retailer_mrp' => 'required|numeric',
            'qty' => 'nullable|integer'
        ]);

        DB::beginTransaction();

        try {

            $data = $request->all();
            $data['supplier_id'] = auth('supplier')->id();

            $data['base_price'] = $request->base_price;

            $initialQty = $request->qty ?? 0;
            $data['current_stock'] = $initialQty;

            $catalog = SupplierItemCatalog::create($data);

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
        $catalog = SupplierItemCatalog::where('supplier_id', auth('supplier')->id())
            ->findOrFail($id);

        $request->validate([
            'item_id' => 'required',
            'batch_no' => 'required|unique:supplier_item_catalogs,batch_no,' . $id,
            'expiry_date' => 'required|date|after:today',
            'purchase_price' => 'required|numeric',
            'retailer_price' => 'required|numeric',
            'retailer_mrp' => 'nullable|numeric',
            'gst_percent' => 'nullable|numeric',
            'base_price' => 'required|numeric',
            'qty' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();

        try {

            $oldStock = $catalog->current_stock ?? 0;

            $catalog->update([
                'item_id' => $request->item_id,
                'batch_no' => $request->batch_no,
                'expiry_date' => $request->expiry_date,
                'purchase_price' => $request->purchase_price,
                'retailer_price' => $request->retailer_price,
                'base_price' => $request->base_price,
                'retailer_mrp' => $request->retailer_mrp,
                'gst_percent' => $request->gst_percent,
            ]);

            if ($request->filled('qty')) {

                $newStock = (int) $request->qty;

                if ($newStock != $oldStock) {

                    $stock = SupplierStock::where('supplier_item_catalog_id', $catalog->id)
                        ->where('type', 'purchase')
                        ->first();

                    if ($stock) {
                        $stock->update([
                            'qty' => $newStock,
                            'note' => 'Updated from edit'
                        ]);
                    } else {
                        SupplierStock::create([
                            'supplier_item_catalog_id' => $catalog->id,
                            'qty' => $newStock,
                            'type' => 'purchase',
                            'note' => 'Initial stock from edit'
                        ]);
                    }

                    $catalog->update([
                        'current_stock' => $newStock
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('supplier.catalogs.index')
                ->with('success', 'Catalog + Stock updated successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Catalog update failed', [
                'error' => $e->getMessage()
            ]);

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $catalog = SupplierItemCatalog::where('supplier_id', auth('supplier')->id())->findOrFail($id);
        $catalog->delete();

        return back()->with('success', 'Deleted successfully');
    }
}
