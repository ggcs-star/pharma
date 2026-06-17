<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeSectionController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | SECTION LIST
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $sections = HomeSection::with([
                'category',
                'items'
            ])
            ->orderBy('sort_order')
            ->paginate(20);

        return view(
            'master.home-sections.index',
            compact('sections')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE FORM
    |--------------------------------------------------------------------------
    */

    public function create()
    {

        $categories = Category::orderBy('name')->get();

        /*
        |--------------------------------------------------------------------------
        | ONLY PURCHASED / STOCK AVAILABLE PRODUCTS
        |--------------------------------------------------------------------------
        */

        $items = Item::with('batches')

            ->whereHas('batches', function ($q) {

                $q->where('stock', '>', 0)

                  ->where('expiry_date', '>', now());

            })

            ->orderBy('name')

            ->get();

        return view(
            'master.home-sections.create',
            compact(
                'categories',
                'items'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE SECTION
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {

        $request->validate([

            'title' => 'required|string|max:255',

            'type' => 'required'
        ]);

        $section = HomeSection::create([

            'title' => $request->title,

            'slug' => Str::slug($request->title),

            'type' => $request->type,

            'category_id' => $request->category_id,

            'sort_order' => $request->sort_order ?? 0,

            'is_active' => $request->has('is_active'),
        ]);


        /*
        |--------------------------------------------------------------------------
        | MANUAL PRODUCTS
        |--------------------------------------------------------------------------
        */

        if(
            $request->type === 'manual'
            &&
            !empty($request->products)
        ){

            $section->items()->sync(
                $request->products
            );
        }

        return redirect()
            ->route('home-sections.index')
            ->with(
                'success',
                'Section Created Successfully'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT FORM
    |--------------------------------------------------------------------------
    */

    public function edit(HomeSection $homeSection)
    {

        $categories = Category::orderBy('name')->get();

        /*
        |--------------------------------------------------------------------------
        | ONLY PURCHASED / STOCK AVAILABLE PRODUCTS
        |--------------------------------------------------------------------------
        */

        $items = Item::with('batches')

            ->whereHas('batches', function ($q) {

                $q->where('stock', '>', 0)

                  ->where('expiry_date', '>', now());

            })

            ->orderBy('name')

            ->get();

        return view(
            'master.home-sections.edit',
            compact(
                'homeSection',
                'categories',
                'items'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE SECTION
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        HomeSection $homeSection
    )
    {

        $request->validate([

            'title' => 'required|string|max:255',

            'type' => 'required'
        ]);

        $homeSection->update([

            'title' => $request->title,

            'slug' => Str::slug($request->title),

            'type' => $request->type,

            'category_id' => $request->category_id,

            'sort_order' => $request->sort_order ?? 0,

            'is_active' => $request->has('is_active'),
        ]);


        /*
        |--------------------------------------------------------------------------
        | MANUAL PRODUCTS
        |--------------------------------------------------------------------------
        */

        if($request->type === 'manual'){

            $homeSection->items()->sync(
                $request->products ?? []
            );
        }

        else{

            $homeSection->items()->detach();
        }

        return redirect()
            ->route('home-sections.index')
            ->with(
                'success',
                'Section Updated Successfully'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE SECTION
    |--------------------------------------------------------------------------
    */

    public function destroy(HomeSection $homeSection)
    {

        $homeSection->items()->detach();

        $homeSection->delete();

        return back()->with(
            'success',
            'Section Deleted Successfully'
        );
    }
}