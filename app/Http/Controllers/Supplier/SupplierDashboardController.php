<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\SupplierItemCatalog;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\SupplierStock;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;
class SupplierDashboardController extends Controller
{
   public function index()
{
    $supplierId = auth('supplier')->id();

    $catalogs = SupplierItemCatalog::with('stocks')
        ->where('supplier_id', $supplierId)
        ->get();

    $totalItems = $catalogs->count();

    $totalStock = $catalogs->sum('current_stock');

    $totalPurchase = $catalogs->flatMap->stocks
        ->where('type', 'purchase')
        ->sum('qty');

    $totalSale = $catalogs->flatMap->stocks
        ->where('type', 'sale')
        ->sum('qty');

    $lowStock = $catalogs->where('current_stock', '<', 10)->count();

    $expired = $catalogs->filter(fn($c) => $c->expiry_date && $c->expiry_date->isPast())->count();

    $recentStocks = SupplierStock::with('catalog.item')
        ->latest()
        ->take(10)
        ->get();
// dd($recentStocks);
    return view('supplier.dashboard', compact(
        'totalItems',
        'totalStock',
        'totalPurchase',
        'totalSale',
        'lowStock',
        'expired',
        'recentStocks'
    ));
}
}
