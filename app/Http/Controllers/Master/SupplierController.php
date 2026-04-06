<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Supplier List
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        try {

            $suppliers = Supplier::latest()->paginate(10);

            return view('master.suppliers.index', compact('suppliers'));

        } catch (\Exception $e) {

            return back()->with('error',$e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Create Form
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('master.suppliers.create');
    }


    /*
    |--------------------------------------------------------------------------
    | Store Supplier (🔥 FULL ERP FIELDS)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|max:15',
                'gstin' => 'nullable|string|max:20',
            ]);

            Supplier::create([

                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,

                // 🔥 IMPORTANT FIELDS
'gst_in' => $request->gst_in,
                'drug_license' => $request->drug_license,
                'supplier_code' => $request->supplier_code,

                // 💰 ACCOUNT / CREDIT
                'credit_period' => $request->credit_period,
                'account_no' => $request->account_no,
                'ifsc_code' => $request->ifsc_code,

                // 🧾 TEMPLATE
                'template_id' => $request->template_id,

                'status' => 1
            ]);

            DB::commit();

            return redirect()
                ->route('suppliers.index')
                ->with('success','Supplier Added Successfully');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Supplier
    |--------------------------------------------------------------------------
    */
    public function edit(Supplier $supplier)
    {
        return view('master.suppliers.edit', compact('supplier'));
    }


    /*
    |--------------------------------------------------------------------------
    | Update Supplier (🔥 FULL FIX)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Supplier $supplier)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|max:15',
                'gstin' => 'nullable|string|max:20',
            ]);

            $supplier->update([

                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,

                // 🔥 IMPORTANT
                'gstin' => $request->gstin,
                'drug_license' => $request->drug_license,
                'supplier_code' => $request->supplier_code,

                // 💰 ACCOUNT
                'credit_period' => $request->credit_period,
                'account_no' => $request->account_no,
                'ifsc_code' => $request->ifsc_code,

                'template_id' => $request->template_id,
            ]);

            DB::commit();

            return redirect()
                ->route('suppliers.index')
                ->with('success','Supplier Updated Successfully');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Supplier (SAFE ERP)
    |--------------------------------------------------------------------------
    */
    public function destroy(Supplier $supplier)
    {
        DB::beginTransaction();

        try {

            // ❌ PURCHASE CHECK
            if (Purchase::where('supplier_id',$supplier->id)->exists()) {
                return back()->with('error','Cannot delete: Purchase exists');
            }

            // ❌ STOCK CHECK
            if (Batch::where('supplier_id',$supplier->id)->exists()) {
                return back()->with('error','Cannot delete: Stock exists');
            }

            $supplier->delete();

            DB::commit();

            return redirect()
                ->route('suppliers.index')
                ->with('success','Supplier Deleted Successfully');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }
}