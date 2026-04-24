<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierItemCatalog;
use App\Models\Supplier;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $query = SupplierItemCatalog::with([
                'supplier',
                'item'
            ])
            ->where('is_active', 1)
            ->whereNotNull('item_id');

        /*
        |--------------------------------------------------------------------------
        | Search Medicine
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where(
                    'name',
                    'like',
                    '%' . $request->search . '%'
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Supplier Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('supplier_id')) {
            $query->where(
                'supplier_id',
                $request->supplier_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Minimum Stock Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('stock')) {
            $query->where(
                'current_stock',
                '>=',
                $request->stock
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        if ($request->sort == 'low_price') {
            $query->orderBy('retailer_price', 'asc');
        } elseif ($request->sort == 'high_stock') {
            $query->orderBy('current_stock', 'desc');
        } else {
            $query->latest();
        }

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        $products = $query->paginate(20);

        /*
        |--------------------------------------------------------------------------
        | Supplier Dropdown List
        |--------------------------------------------------------------------------
        */

        $suppliers = Supplier::orderBy('name')
            ->get(['id', 'name']);

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'marketplace.index',
            compact(
                'products',
                'suppliers'
            )
        );
    }
}