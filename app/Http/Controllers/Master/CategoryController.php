<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Category List
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        try {

            $categories = Category::latest()->paginate(10);

            return view('master.categories.index', compact('categories'));

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

            return view('master.categories.create');

        } catch (\Exception $e) {

            return back()->with('error',$e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Store Category
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {

        DB::beginTransaction();

        try {

            $request->validate([
                'name' => 'required|unique:categories,name'
            ]);

            Category::create([
                'name' => $request->name
            ]);

            DB::commit();

            return redirect()
                ->route('categories.index')
                ->with('success','Category Added Successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error',$e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Category
    |--------------------------------------------------------------------------
    */

    public function edit(Category $category)
    {
        try {

            return view('master.categories.edit', compact('category'));

        } catch (\Exception $e) {

            return back()->with('error',$e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Update Category
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Category $category)
    {

        DB::beginTransaction();

        try {

            $request->validate([
                'name' => 'required|unique:categories,name,' . $category->id
            ]);

            $category->update([
                'name' => $request->name
            ]);

            DB::commit();

            return redirect()
                ->route('categories.index')
                ->with('success','Category Updated Successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error',$e->getMessage());
        }

    }


    /*
    |--------------------------------------------------------------------------
    | Delete Category
    |--------------------------------------------------------------------------
    */

    public function destroy(Category $category)
    {

        DB::beginTransaction();

        try {

            $category->delete();

            DB::commit();

            return redirect()
                ->route('categories.index')
                ->with('success','Category Deleted Successfully');

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

$category = Category::create([
'name'=>$request->name
]);

return response()->json($category);

}
}