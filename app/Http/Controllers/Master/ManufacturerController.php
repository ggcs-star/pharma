<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManufacturerController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Manufacturer List
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        try {

            $manufacturers = Manufacturer::latest()->paginate(10);

            return view('master.manufacturers.index', compact('manufacturers'));

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

            return view('master.manufacturers.create');

        } catch (\Exception $e) {

            return back()->with('error',$e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Store Manufacturer
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {

        DB::beginTransaction();

        try {

            $request->validate([
                'name' => 'required|string|max:255|unique:manufacturers,name'
            ]);

            Manufacturer::create([
                'name'   => $request->name,
                'status' => 1
            ]);

            DB::commit();

            return redirect()
                ->route('manufacturers.index')
                ->with('success','Manufacturer Created Successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error',$e->getMessage());
        }

    }


    /*
    |--------------------------------------------------------------------------
    | Edit Manufacturer
    |--------------------------------------------------------------------------
    */

    public function edit(Manufacturer $manufacturer)
    {
        try {

            return view('master.manufacturers.edit', compact('manufacturer'));

        } catch (\Exception $e) {

            return back()->with('error',$e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Update Manufacturer
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Manufacturer $manufacturer)
    {

        DB::beginTransaction();

        try {

            $request->validate([
                'name' => 'required|string|max:255|unique:manufacturers,name,' . $manufacturer->id
            ]);

            $manufacturer->update([
                'name' => $request->name
            ]);

            DB::commit();

            return redirect()
                ->route('manufacturers.index')
                ->with('success','Manufacturer Updated Successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error',$e->getMessage());
        }

    }


    /*
    |--------------------------------------------------------------------------
    | Delete Manufacturer
    |--------------------------------------------------------------------------
    */

    public function destroy(Manufacturer $manufacturer)
    {

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | ERP Safety Check
            | Manufacturer delete नहीं होगा अगर items linked हैं
            |--------------------------------------------------------------------------
            */

            $itemExists = Item::where('manufacturer_id',$manufacturer->id)->exists();

            if ($itemExists) {

                return back()->with(
                    'error',
                    'Cannot delete manufacturer because items are linked.'
                );

            }

            $manufacturer->delete();

            DB::commit();

            return redirect()
                ->route('manufacturers.index')
                ->with('success','Manufacturer Deleted Successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error',$e->getMessage());
        }

    }

    public function storeAjax(Request $request)
{

$request->validate([
'name'=>'required|string|max:255'
]);

$manufacturer = Manufacturer::create([
'name'=>$request->name
]);

return response()->json($manufacturer);

}
}
