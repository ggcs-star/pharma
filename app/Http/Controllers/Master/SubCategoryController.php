<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{

public function index()
{
$subCategories = SubCategory::with('category')
->latest()
->paginate(10);

return view('master.sub_categories.index',compact('subCategories'));
}


public function create()
{
$categories = Category::pluck('name','id');

return view('master.sub_categories.create',compact('categories'));
}


public function store(Request $request)
{

$request->validate([

'name'=>'required|max:255',
'category_id'=>'required|exists:categories,id'

]);

SubCategory::create([

'name'=>$request->name,
'category_id'=>$request->category_id

]);

return redirect()
->route('sub-categories.index')
->with('success','Sub Category Created');

}

public function storeAjax(Request $request)
{

$request->validate([
'name'=>'required|string|max:255',
'category_id'=>'required'
]);

$sub = SubCategory::create([
'name'=>$request->name,
'category_id'=>$request->category_id
]);

return response()->json($sub);

}
}