<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Batch;
use App\Models\Item;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\CustomerLedger;
use App\Models\SupplierLedger;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
/*
|--------------------------------------------------------------------------
| DASHBOARD DATE FILTER
|--------------------------------------------------------------------------
*/

$range = request('range', 7);

$startDate = now();

if ($range == 7) {

    $startDate = now()->subDays(7);

} elseif ($range == 30) {

    $startDate = now()->subDays(30);

} elseif ($range == 90) {

    $startDate = now()->subDays(90);

} elseif ($range == 'month') {

    $startDate = now()->startOfMonth();

} elseif ($range == 'today') {

    $startDate = now()->startOfDay();

}



        
        // ===============================
        // SALES (Customer Purchases) ✅
        // ===============================
       /*
|--------------------------------------------------------------------------
| OFFLINE SALES
|--------------------------------------------------------------------------
*/

$todayOfflineSales = DB::table('sales')
->whereDate('bill_date', '>=', $startDate)

    ->sum('net_amount');

$monthlyOfflineSales = DB::table('sales')
    ->whereBetween('bill_date', [$monthStart, $monthEnd])
    ->sum('net_amount');

$offlineSalesCount = DB::table('sales')
    ->whereDate('bill_date', '>=', $startDate)
    ->count();



/*
|--------------------------------------------------------------------------
| ONLINE SALES
|--------------------------------------------------------------------------
*/


$todayOnlineSales = DB::table('orders')
    ->whereDate('created_at', '>=', $startDate)
    ->sum('total');



$monthlyOnlineSales = DB::table('orders')
    ->whereBetween('created_at', [$monthStart, $monthEnd])
    ->sum('total');


$onlineOrdersCount = DB::table('orders')
    ->whereDate('created_at', '>=', $startDate)
    ->count();



/*
|--------------------------------------------------------------------------
| TOTAL SALES
|--------------------------------------------------------------------------
*/

$todayTotalSales =
    $todayOfflineSales + $todayOnlineSales;

$monthlyTotalSales =
    $monthlyOfflineSales + $monthlyOnlineSales;
      /*
|--------------------------------------------------------------------------
| SALES TREND
|--------------------------------------------------------------------------
*/

$yesterdayOfflineSales = DB::table('sales')
    ->whereDate('bill_date', now()->subDay())
    ->sum('net_amount');

$yesterdayOnlineSales = DB::table('orders')
    ->whereDate('created_at', now()->subDay())
    ->sum('total');

/*
|--------------------------------------------------------------------------
| YESTERDAY TOTAL SALES
|--------------------------------------------------------------------------
*/

$yesterdayTotalSales =
    $yesterdayOfflineSales + $yesterdayOnlineSales;

/*
|--------------------------------------------------------------------------
| TODAY SALES TREND %
|--------------------------------------------------------------------------
*/

$todaySalesTrend =
    $yesterdayTotalSales > 0

    ? round(
        (
            ($todayTotalSales - $yesterdayTotalSales)
            / $yesterdayTotalSales
        ) * 100,
        1
    )

    : 0;

/*
|--------------------------------------------------------------------------
| COMPARISON TEXT
|--------------------------------------------------------------------------
*/

$todayComparison =
    $todaySalesTrend > 0
        ? '+' . $todaySalesTrend . '%'
        : $todaySalesTrend . '%';

        // ===============================
        // PURCHASE (Supplier Purchases)
        // ===============================
        $todayPurchase = Purchase::whereDate('created_at', $today)
            ->sum('net_amount');
        
        $purchaseTransactions = Purchase::whereDate('created_at', $today)
            ->count();
            /*
|--------------------------------------------------------------------------
| TOTAL PURCHASE QTY
|--------------------------------------------------------------------------
*/

$totalPurchasedQty = DB::table('purchase_items')
    ->sum('quantity');

/*
|--------------------------------------------------------------------------
| TOTAL OFFLINE SOLD
|--------------------------------------------------------------------------
*/

$totalOfflineSoldQty = DB::table('sales_items')
    ->sum('unit_qty');

/*
|--------------------------------------------------------------------------
| TOTAL ONLINE SOLD
|--------------------------------------------------------------------------
*/

$totalOnlineSoldQty = DB::table('order_items')
    ->sum('qty');

/*
|--------------------------------------------------------------------------
| TOTAL SOLD
|--------------------------------------------------------------------------
*/

$totalSoldQty =
    $totalOfflineSoldQty + $totalOnlineSoldQty;

        // ===============================
        // CUSTOMER OUTSTANDING (Fixed - No paid_amount column)
        // Get from CustomerLedger or calculate from sales
        // ===============================
        $customerOutstanding = 0;
        try {
            // First try to get from CustomerLedger
            $customerOutstanding = CustomerLedger::select(DB::raw('SUM(COALESCE(debit, 0) - COALESCE(credit, 0)) as balance'))
                ->value('balance') ?? 0;
        } catch (\Exception $e) {
            // If ledger table doesn't have debit/credit, try other approach
            try {
                // Try to get latest balance per customer from CustomerLedger
                $customerOutstanding = CustomerLedger::orderBy('id', 'desc')
                    ->get()
                    ->unique('customer_id')
                    ->sum(function($ledger) {
                        // Adjust based on your actual columns
                        return $ledger->debit ?? $ledger->amount ?? 0;
                    });
            } catch (\Exception $e) {
                // If all fails, set to 0
                $customerOutstanding = 0;
            }
        }

        // Overdue customers (based on ledger or sales date)
        $overdueCustomers = 0;
        try {
            $overdueCustomers = CustomerLedger::whereDate('created_at', '<', now()->subDays(30))
                ->distinct('customer_id')
                ->count('customer_id');
        } catch (\Exception $e) {
            $overdueCustomers = Sale::whereDate('bill_date', '<', now()->subDays(30))
                ->distinct('customer_id')
                ->count('customer_id');
        }

        // ===============================
        // SUPPLIER PAYABLE (Fixed - No paid_amount column)
        // ===============================
        $supplierPayable = 0;
        try {
            $supplierPayable = SupplierLedger::select(DB::raw('SUM(COALESCE(debit, 0) - COALESCE(credit, 0)) as balance'))
                ->value('balance') ?? 0;
        } catch (\Exception $e) {
            try {
                $supplierPayable = SupplierLedger::orderBy('id', 'desc')
                    ->get()
                    ->unique('supplier_id')
                    ->sum(function($ledger) {
                        return $ledger->credit ?? $ledger->amount ?? 0;
                    });
            } catch (\Exception $e) {
                $supplierPayable = 0;
            }
        }

        // ===============================
        // STOCK ALERTS
        // ===============================
        $lowStockCount = Batch::where('stock', '<', 10)
            ->where('stock', '>', 0)
            ->count();

        $expiryNearCount = Batch::whereDate('expiry_date', '<=', now()->addDays(30))
            ->whereDate('expiry_date', '>', now())
            ->where('stock', '>', 0)
            ->count();
            $expiredStockCount = Batch::whereDate('expiry_date', '<', now())
    ->where('stock', '>', 0)
    ->count();

$outOfStockCount = Batch::where('stock', '<=', 0)
    ->count();
/*
|--------------------------------------------------------------------------
| TOTAL AVAILABLE STOCK
|--------------------------------------------------------------------------
*/

$totalAvailableStock = DB::table('batches')
    ->selectRaw('
        SUM(
            COALESCE(stock,0)
            + COALESCE(loose_stock,0)
        ) as total_stock
    ')
    ->value('total_stock');
/*
|--------------------------------------------------------------------------
| Expiring Soon Medicines With Image
|--------------------------------------------------------------------------
*/

$expiringSoonBatches = Batch::with('item')
    ->whereDate('expiry_date', '>=', now())
    ->whereDate('expiry_date', '<=', now()->addDays(30))
    ->where('stock', '>', 0)
    ->orderBy('expiry_date', 'asc')
    ->limit(10)
    ->get();

/*
|--------------------------------------------------------------------------
| Expired Medicines With Image
|--------------------------------------------------------------------------
*/

$expiredBatches = Batch::with('item')
    ->whereDate('expiry_date', '<', now())
    ->where('stock', '>', 0)
    ->orderBy('expiry_date', 'asc')
    ->limit(10)
    ->get();

/*
|--------------------------------------------------------------------------
| Out Of Stock Medicines With Image
|--------------------------------------------------------------------------
*/

$outOfStockBatches = Batch::with('item')
    ->where('stock', '<=', 0)
    ->limit(10)
    ->get();

        // ===============================
        // TOTAL COUNTS
        // ===============================
$totalMedicines = DB::table('purchase_items')
    ->distinct('item_id')
    ->count('item_id');
        $totalCustomers = Customer::count();
        $totalSuppliers = Supplier::count();
        
        // Get categories count if table exists
        $totalCategories = 0;
        if (DB::getSchemaBuilder()->hasTable('categories')) {
            $totalCategories = DB::table('categories')->count();
        }

        // ===============================
        // RECENT TRANSACTIONS (Sales)
        // ===============================
/*
|--------------------------------------------------------------------------
| RECENT OFFLINE SALES
|--------------------------------------------------------------------------
*/

$recentOfflineSales = DB::table('sales')
    ->leftJoin(
        'customers',
        'customers.id',
        '=',
        'sales.customer_id'
    )
    ->select([
        'sales.bill_number',
        'sales.net_amount',
        'sales.created_at',
        'customers.name as customer_name',
    ])
    ->latest('sales.id')
    ->limit(5)
    ->get();

/*
|--------------------------------------------------------------------------
| RECENT ONLINE ORDERS
|--------------------------------------------------------------------------
*/

$recentOnlineOrders = DB::table('orders')
    ->leftJoin(
        'users',
        'users.id',
        '=',
        'orders.user_id'
    )
    ->select([
        'orders.id',
        'orders.total',
        'orders.created_at',
        'users.name as customer_name',
    ])
    ->latest('orders.id')
    ->limit(5)
    ->get();

/*
|--------------------------------------------------------------------------
| CHART DATA
|--------------------------------------------------------------------------
*/

$chartLabels = [];

$offlineChartData = [];

$onlineChartData = [];

for ($i = 6; $i >= 0; $i--) {

    $date = now()->subDays($i)->toDateString();

    $chartLabels[] =
        now()->subDays($i)->format('D');

    /*
    |--------------------------------------------------------------------------
    | OFFLINE SALES
    |--------------------------------------------------------------------------
    */

    $offlineSale = DB::table('sales')
        ->whereDate('bill_date', $date)
        ->sum('net_amount');

    /*
    |--------------------------------------------------------------------------
    | ONLINE SALES
    |--------------------------------------------------------------------------
    */
$onlineSale = DB::table('orders')
    ->whereDate('created_at', $date)
    ->sum('total');

    $offlineChartData[] = $offlineSale;

    $onlineChartData[] = $onlineSale;
}
/*
|--------------------------------------------------------------------------
| OVERALL ERP STATS
|--------------------------------------------------------------------------
*/

$totalSalesBills = DB::table('sales')
    ->count();
$totalOfflineOrders = $totalSalesBills;



$totalSalesAmount = DB::table('sales')
    ->sum('net_amount');

$totalPurchases = DB::table('purchases')
    ->count();

$totalPurchaseAmount = DB::table('purchases')
    ->sum('net_amount');

$totalOnlineOrders = DB::table('orders')
    ->count();

$totalOnlineRevenue = DB::table('orders')
    ->sum('total');

$totalStock = DB::table('batches')
    ->sum('stock');

$totalLooseStock = DB::table('batches')
    ->sum('loose_stock');

/*
|--------------------------------------------------------------------------
| TODAY STATS
|--------------------------------------------------------------------------
*/

$todayCustomers = DB::table('sales')
->whereDate('bill_date', '>=', $startDate)


    ->distinct('customer_id')
    ->count('customer_id');

$todayOrders = DB::table('orders')
->whereDate('created_at', '>=', $startDate)

    ->count();

/*
|--------------------------------------------------------------------------
| MONTHLY STATS
|--------------------------------------------------------------------------
*/

$monthlyPurchases = DB::table('purchases')
    ->whereBetween('created_at', [$monthStart, $monthEnd])
    ->sum('net_amount');

$monthlyOrders = DB::table('orders')
    ->whereBetween('created_at', [$monthStart, $monthEnd])
    ->count();

/*
|--------------------------------------------------------------------------
| TOP SELLING MEDICINES
|--------------------------------------------------------------------------
*/

$topSellingMedicines = DB::table('sales_items')
    ->join('items', 'items.id', '=', 'sales_items.item_id')
    ->select(
        'items.name',
        DB::raw('SUM(sales_items.unit_qty) as total_qty')
    )
    ->groupBy('items.name')
    ->orderByDesc('total_qty')
    ->limit(5)
    ->get();

/*
|--------------------------------------------------------------------------
| RECENT CUSTOMERS
|--------------------------------------------------------------------------
*/

$recentCustomers = DB::table('customers')
    ->latest('id')
    ->limit(5)
    ->get();
return view('dashboard', compact(

    /*
    |--------------------------------------------------------------------------
    | SALES
    |--------------------------------------------------------------------------
    */

    'todayOfflineSales',
    'monthlyOfflineSales',
    'totalOfflineOrders',
    'offlineSalesCount',

    'todayOnlineSales',
    'monthlyOnlineSales',
    'onlineOrdersCount',

    'todayTotalSales',
    'monthlyTotalSales',

    /*
    |--------------------------------------------------------------------------
    | PURCHASE
    |--------------------------------------------------------------------------
    */

    'todayPurchase',
    'purchaseTransactions',
    'totalPurchasedQty',

    /*
    |--------------------------------------------------------------------------
    | SOLD
    |--------------------------------------------------------------------------
    */

    'totalOfflineSoldQty',
    'totalOnlineSoldQty',
    'totalSoldQty',

    /*
    |--------------------------------------------------------------------------
    | STOCK
    |--------------------------------------------------------------------------
    */

    'totalAvailableStock',

    'lowStockCount',
    'expiryNearCount',
    'expiredStockCount',
    'outOfStockCount',

    /*
    |--------------------------------------------------------------------------
    | OTHER
    |--------------------------------------------------------------------------
    */

    'todaySalesTrend',
    'todayComparison',

    'customerOutstanding',
    'overdueCustomers',
    'supplierPayable',

    'totalMedicines',
    'totalCustomers',
    'totalSuppliers',
    'totalCategories',

    /*
    |--------------------------------------------------------------------------
    | RECENT
    |--------------------------------------------------------------------------
    */

    'recentOfflineSales',
    'recentOnlineOrders',

    /*
    |--------------------------------------------------------------------------
    | EXPIRY
    |--------------------------------------------------------------------------
    */

    'expiringSoonBatches',
    'expiredBatches',
    'outOfStockBatches',

    /*
    |--------------------------------------------------------------------------
    | CHART
    |--------------------------------------------------------------------------
    */
'chartLabels',
'offlineChartData',
'onlineChartData',

/*
|--------------------------------------------------------------------------
| ERP STATS
|--------------------------------------------------------------------------
*/

'totalSalesBills',
'totalSalesAmount',

'totalPurchases',
'totalPurchaseAmount',

'totalOnlineOrders',
'totalOnlineRevenue',

'totalStock',
'totalLooseStock',

'todayCustomers',
'todayOrders',

'monthlyPurchases',
'monthlyOrders',

'topSellingMedicines',
'recentCustomers'
));

    }
}