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
    | Supplier List with Search & Filter
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        try {
            $query = Supplier::query();

            // 🔍 SEARCH FUNCTIONALITY
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('supplier_code', 'like', "%{$search}%")
                      ->orWhere('gst_in', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('drug_license', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // 📊 BALANCE FILTER
            if ($request->filled('balance_filter')) {
                $balanceFilter = $request->balance_filter;
                
                if ($balanceFilter === 'positive') {
                    $query->whereHas('ledgers', function($q) {
                        $q->havingRaw('SUM(debit) - SUM(credit) > 0');
                    });
                } elseif ($balanceFilter === 'negative') {
                    $query->whereHas('ledgers', function($q) {
                        $q->havingRaw('SUM(debit) - SUM(credit) < 0');
                    });
                } elseif ($balanceFilter === 'zero') {
                    $query->whereDoesntHave('ledgers')
                          ->orWhereHas('ledgers', function($q) {
                              $q->havingRaw('SUM(debit) - SUM(credit) = 0');
                          });
                }
            }

            // 🔽 SORTING
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            $allowedSorts = ['name', 'supplier_code', 'gst_in', 'phone', 'created_at'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // 📄 PAGINATION (10, 25, 50, 100)
            $perPage = $request->get('per_page', 10);
            $suppliers = $query->paginate($perPage)->withQueryString();

            return view('master.suppliers.index', compact('suppliers'));

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
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
                'gst_in' => 'nullable|string|max:20',
                'supplier_code' => 'nullable|string|max:50|unique:suppliers,supplier_code',
                'email' => 'nullable|email|max:255',
            ]);

            Supplier::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'gst_in' => $request->gst_in,
                'drug_license' => $request->drug_license,
                'supplier_code' => $request->supplier_code,
                'credit_period' => $request->credit_period,
                'account_no' => $request->account_no,
                'ifsc_code' => $request->ifsc_code,
                'template_id' => $request->template_id,
                'status' => 1
            ]);

            DB::commit();
            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier Added Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
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
    | Update Supplier
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Supplier $supplier)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|max:15',
                'gst_in' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
            ]);

            $supplier->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'gst_in' => $request->gst_in,
                'drug_license' => $request->drug_license,
                'supplier_code' => $request->supplier_code,
                'credit_period' => $request->credit_period,
                'account_no' => $request->account_no,
                'ifsc_code' => $request->ifsc_code,
                'template_id' => $request->template_id,
            ]);

            DB::commit();
            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier Updated Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
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
            if (Purchase::where('supplier_id', $supplier->id)->exists()) {
                return back()->with('error', 'Cannot delete: Purchase exists');
            }

            if (Batch::where('supplier_id', $supplier->id)->exists()) {
                return back()->with('error', 'Cannot delete: Stock exists');
            }

            $supplier->delete();
            DB::commit();

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier Deleted Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}