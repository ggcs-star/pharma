<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Customer List
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        try {

            $customers = Customer::with('doctor')
                ->latest()
                ->paginate(10);

            return view('master.customers.index', compact('customers'));

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
        try {

            $doctors = Doctor::all();

            return view('master.customers.create', compact('doctors'));

        } catch (\Exception $e) {

            return back()->with('error',$e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Store Customer
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
{

    DB::beginTransaction();

    try {

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        /*
        |----------------------------------
        | Generate Customer Code
        |----------------------------------
        */

        $lastCustomer = Customer::latest()->first();

        $nextId = $lastCustomer ? $lastCustomer->id + 1 : 1;

        $customerCode = 'C' . str_pad($nextId,4,'0',STR_PAD_LEFT);


        Customer::create([

            'customer_code'      => $customerCode,
            'name'               => $request->name,
            'contact'            => $request->contact,
            'flat_number'        => $request->flat_number,
            'discount'           => $request->discount,
            'customer_type'      => $request->customer_type,
            'address'            => $request->address,
            'doctor_id'          => $request->doctor_id,
            'preferred_language' => $request->preferred_language,
            'city'               => $request->city,
            'pincode'            => $request->pincode,
            'last_buy_date'      => null
        ]);


        DB::commit();

        return redirect()
            ->route('customers.index')
            ->with('success','Customer Added Successfully');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->with('error',$e->getMessage());
    }

}


    /*
    |--------------------------------------------------------------------------
    | Edit Customer
    |--------------------------------------------------------------------------
    */

    public function edit(Customer $customer)
    {
        try {

            $doctors = Doctor::all();

            return view('master.customers.edit', compact('customer','doctors'));

        } catch (\Exception $e) {

            return back()->with('error',$e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Update Customer
    |--------------------------------------------------------------------------
    */

   public function update(Request $request, Customer $customer)
{

    DB::beginTransaction();

    try {

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $customer->update([

            'name'               => $request->name,
            'contact'            => $request->contact,
            'flat_number'        => $request->flat_number,
            'discount'           => $request->discount,
            'customer_type'      => $request->customer_type,
            'address'            => $request->address,
            'doctor_id'          => $request->doctor_id,
            'preferred_language' => $request->preferred_language,
            'city'               => $request->city,
            'pincode'            => $request->pincode

        ]);

        DB::commit();

        return redirect()
            ->route('customers.index')
            ->with('success','Customer Updated Successfully');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->with('error',$e->getMessage());
    }

}


    /*
    |--------------------------------------------------------------------------
    | Delete Customer
    |--------------------------------------------------------------------------
    */

    public function destroy(Customer $customer)
    {

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | ERP Safety Check
            |--------------------------------------------------------------------------
            */

            if ($customer->current_balance != 0) {

                return back()->with(
                    'error',
                    'Cannot delete customer with pending balance.'
                );

            }


            $customer->delete();


            DB::commit();


            return redirect()
                ->route('customers.index')
                ->with('success','Customer Deleted Successfully');


        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error',$e->getMessage());
        }

    }
public function ajaxStore(Request $request)
{
    DB::beginTransaction();

    try {

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        // 🔥 DUPLICATE MOBILE CHECK (ADD THIS)
        if ($request->mobile) {
            $existing = Customer::where('contact', $request->mobile)->first();
            if ($existing) {
                return response()->json([
                    'success' => true,
                    'customer' => $existing
                ]);
            }
        }

        // 🔥 CUSTOMER CODE
        $lastCustomer = Customer::latest()->first();
        $nextId = $lastCustomer ? $lastCustomer->id + 1 : 1;

        $customerCode = 'C' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $customer = Customer::create([
            'customer_code' => $customerCode,
            'name'          => $request->name,
            'contact'       => $request->mobile,
            'address'       => $request->address,
'customer_type' => 'regular',
            'last_buy_date' => null
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'customer' => $customer
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
}