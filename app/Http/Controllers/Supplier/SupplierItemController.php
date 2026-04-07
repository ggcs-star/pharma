<?php
namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\SupplierItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupplierItemController extends Controller
{
    public function index()
    {
        $items = SupplierItem::where('supplier_id', auth('supplier')->id())->latest()->get();
        return view('supplier.items.index', compact('items'));
    }

    public function create()
    {
        return view('supplier.items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'selling_price' => 'required|numeric',
            'main_image' => 'nullable|image'
        ]);

        $data = $request->all();
        $data['supplier_id'] = auth('supplier')->id();
        $data['slug'] = Str::slug($request->name);

        // Image upload
        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('supplier_items', 'public');
        }

        SupplierItem::create($data);

        return redirect()->route('supplier.items.index')->with('success', 'Item created');
    }

    public function edit($id)
    {
        $item = SupplierItem::where('supplier_id', auth('supplier')->id())->findOrFail($id);
        return view('supplier.items.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = SupplierItem::where('supplier_id', auth('supplier')->id())->findOrFail($id);

        $request->validate([
            'name' => 'required',
            'selling_price' => 'required|numeric',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('main_image')) {
            if ($item->main_image) {
                Storage::disk('public')->delete($item->main_image);
            }

            $data['main_image'] = $request->file('main_image')->store('supplier_items', 'public');
        }

        $item->update($data);

        return redirect()->route('supplier.items.index')->with('success', 'Item updated');
    }

    public function destroy($id)
    {
        $item = SupplierItem::where('supplier_id', auth('supplier')->id())->findOrFail($id);

        if ($item->main_image) {
            Storage::disk('public')->delete($item->main_image);
        }

        $item->delete();

        return back()->with('success', 'Item deleted');
    }
}