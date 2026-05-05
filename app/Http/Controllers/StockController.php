<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
class StockController extends Controller
{



 public function index(Request $request)
{
    $search = $request->search;
    $fromDate = $request->from_date;
    $toDate = $request->to_date;

    $stocks = $this->getItemStock($search, $fromDate, $toDate);
    $batchStocks = $this->getBatchStock($fromDate, $toDate);

    /*
    |--------------------------------------------------------------------------
    | Customer Sales History Mapping
    |--------------------------------------------------------------------------
    */

    $customerSales = [];

    foreach ($batchStocks as $itemId => $batches) {
        foreach ($batches as $batch) {

            /*
            |--------------------------------------------------------------------------
            | Offline Sales
            |--------------------------------------------------------------------------
            */

           $offlineSales = DB::table('sales_items')
    ->join('sales', 'sales.id', '=', 'sales_items.sale_id')
    ->leftJoin('customers', 'customers.id', '=', 'sales.customer_id')
    ->leftJoin('doctors', 'doctors.id', '=', 'sales.doctor_id')
    ->where('sales_items.batch_id', $batch->id)

    ->select([
        'sales.bill_number as bill_no',
        'sales.created_at',
        'customers.name as customer_name',
        'doctors.name as doctor_name',

        DB::raw('sales_items.quantity as quantity'),

        'sales_items.sale_type',
        'sales_items.unit_qty',
        'sales_items.selling_price',

        DB::raw("'cash' as payment_mode"),
        DB::raw("'completed' as status"),

        DB::raw('
            (
                sales_items.quantity
                * sales_items.selling_price
            ) as total_amount
        '),
    ])
    ->get();
            /*
            |--------------------------------------------------------------------------
            | Online Orders
            |--------------------------------------------------------------------------
            */

            $onlineSales = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->leftJoin('users', 'users.id', '=', 'orders.user_id')
                ->where('order_items.batch_id', $batch->id)

                ->select([
                    'orders.id as order_no',
                    'orders.created_at as order_date',
                    'orders.payment_mode',
                    'orders.status as order_status',
                    'users.name as customer_name',

                    'order_items.qty as quantity',
                    'order_items.price as selling_price',

                    DB::raw('(order_items.qty * order_items.price) as total_amount'),
                ])
                ->get();

            $customerSales[$batch->id] = [
                'offline_sales' => $offlineSales,
                'online_sales' => $onlineSales,
            ];
        }
    }

    return view('stock.index', compact(
        'stocks',
        'batchStocks',
        'customerSales'
    ));
}

    

private function getItemStock($search = null, $fromDate = null, $toDate = null)
{
    return \App\Models\Item::query()

        /*
        |--------------------------------------------------------------------------
        | Only items that exist in stock ledger
        |--------------------------------------------------------------------------
        */
->where(function ($q) {
    $q->whereIn('items.id', function ($sub) {
        $sub->select('item_id')
            ->from('stock_movements');
    })
    ->orWhereIn('items.id', function ($sub) {
        $sub->select('item_id')
            ->from('batches');
    });
})


        /*
        |--------------------------------------------------------------------------
        | Search Filter
        |--------------------------------------------------------------------------
        */

        ->when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        })

        /*
        |--------------------------------------------------------------------------
        | Join Stock Movement Summary
        |--------------------------------------------------------------------------
        */

        ->leftJoinSub(
            $this->stockMovementItemSubQuery($fromDate, $toDate),
            's',
            function ($join) {
                $join->on('items.id', '=', 's.item_id');
            }
        )
->leftJoin('item_packings', 'items.id', '=', 'item_packings.item_id')
        /*
        |--------------------------------------------------------------------------
        | Final Select
        |--------------------------------------------------------------------------
        */

        ->select([
    'items.*',

    'item_packings.qty as pack_qty',
    'item_packings.packaging_detail',
    'item_packings.product_form',

    DB::raw('COALESCE(s.total_purchase, 0) as total_purchase'),
    DB::raw('COALESCE(s.total_sale, 0) as total_sale'),
    DB::raw('COALESCE(s.total_return, 0) as total_return'),
    DB::raw('COALESCE(s.available_stock, 0) as available_stock'),
])
        ->orderBy('items.name')
        ->paginate(10);
}
private function stockMovementItemSubQuery($fromDate = null, $toDate = null)
{
    return DB::table('stock_movements')
        ->select(
            'item_id',

            /*
            |--------------------------------------------------------------------------
            | Total Purchase
            |--------------------------------------------------------------------------
            */

            DB::raw("
                SUM(
                    CASE
                        WHEN type = 'purchase' AND direction = 'in'
                        THEN quantity
                        ELSE 0
                    END
                ) as total_purchase
            "),

            /*
            |--------------------------------------------------------------------------
            | Total Sale
            |--------------------------------------------------------------------------
            */

            DB::raw("
                SUM(
                    CASE
                        WHEN type = 'sale' AND direction = 'out'
                        THEN ABS(quantity)
                        ELSE 0
                    END
                ) as total_sale
            "),

            /*
            |--------------------------------------------------------------------------
            | Total Return
            |--------------------------------------------------------------------------
            */

            DB::raw("
                SUM(
                    CASE
                     WHEN type = 'sale_return'
            THEN ABS(quantity)

            WHEN type = 'purchase_return'
            THEN -ABS(quantity)

                        ELSE 0
                    END
                ) as total_return
            "),

            /*
            |--------------------------------------------------------------------------
            | Final Available Stock
            |--------------------------------------------------------------------------
            */

            DB::raw("
                SUM(quantity) as available_stock
            ")
        )

        ->when($fromDate, function ($q) use ($fromDate) {
            $q->whereDate('created_at', '>=', $fromDate);
        })

        ->when($toDate, function ($q) use ($toDate) {
            $q->whereDate('created_at', '<=', $toDate);
        })

        ->groupBy('item_id');
}
private function onlineOrderBatchSubQuery($fromDate = null, $toDate = null)
{
    return DB::table('order_items')
        ->select(
            'batch_id',
            DB::raw('SUM(qty) as total_online_sale')
        )

        ->when($fromDate, function ($q) use ($fromDate) {
            $q->whereDate('created_at', '>=', $fromDate);
        })

        ->when($toDate, function ($q) use ($toDate) {
            $q->whereDate('created_at', '<=', $toDate);
        })

        ->groupBy('batch_id');
}
private function stockMovementBatchSubQuery($fromDate = null, $toDate = null)
{
    return DB::table('stock_movements')
        ->select(
            'batch_id',

            DB::raw("
                SUM(
                    CASE
                        WHEN type = 'purchase' AND direction = 'in'
                        THEN quantity
                        ELSE 0
                    END
                ) as total_purchase
            "),

            DB::raw("
                SUM(
                    CASE
                        WHEN type = 'sale' AND direction = 'out'
                        THEN ABS(quantity)
                        ELSE 0
                    END
                ) as total_sale
            "),

            DB::raw("
                SUM(
                    CASE
                       WHEN type = 'sale_return'
THEN ABS(quantity)

WHEN type = 'purchase_return'
THEN -ABS(quantity)
                        ELSE 0
                    END
                ) as total_return
            "),

            DB::raw("
                SUM(quantity) as available_stock
            ")
        )

        ->when($fromDate, function ($q) use ($fromDate) {
            $q->whereDate('created_at', '>=', $fromDate);
        })

        ->when($toDate, function ($q) use ($toDate) {
            $q->whereDate('created_at', '<=', $toDate);
        })

        ->groupBy('batch_id');
}
private function getBatchStock($fromDate = null, $toDate = null)
{
    return DB::table('batches')

        ->leftJoinSub(
            $this->stockMovementBatchSubQuery($fromDate, $toDate),
            's',
            function ($join) {
                $join->on('batches.id', '=', 's.batch_id');
            }
        )
        ->leftJoinSub(
    $this->onlineOrderBatchSubQuery($fromDate, $toDate),
    'o',
    function ($join) {
        $join->on('batches.id', '=', 'o.batch_id');
    }
)

      ->select([
    'batches.id',
    'batches.item_id',
    'batches.batch_code',
    'batches.expiry_date',

    // VERY IMPORTANT
    'batches.stock',
    'batches.loose_stock',
    'batches.mrp',
    'batches.ptr',
    'batches.selling_price',

    DB::raw('COALESCE(s.total_purchase, 0) as total_purchase'),
    DB::raw('COALESCE(s.total_sale, 0) as total_sale'),
    DB::raw('COALESCE(o.total_online_sale, 0) as total_online_sale'),
    DB::raw('COALESCE(s.total_return, 0) as total_return'),
DB::raw("
(
    COALESCE(s.total_purchase, 0)
    - COALESCE(s.total_sale, 0)
    - COALESCE(o.total_online_sale, 0)
    + COALESCE(s.total_return, 0)
) as available_stock
"),
])
        ->orderBy('batches.expiry_date')
        ->get()
        ->groupBy('item_id');
}

    






public function show($itemId)
{
    $item = DB::table('items')
    ->leftJoin('item_packings', 'items.id', '=', 'item_packings.item_id')
    ->where('items.id', $itemId)
    ->select(
        'items.*',
        'item_packings.qty as pack_qty',
        'item_packings.packaging_detail',
        'item_packings.product_form'
    )
    ->first();

    /*
    |--------------------------------------------------------------------------
    | Summary from stock_movements
    |--------------------------------------------------------------------------
    */

    $summary = DB::table('stock_movements')
    
    ->where('item_id', $itemId)
    ->selectRaw("
        SUM(
            CASE
                WHEN type = 'purchase' AND direction = 'in'
                THEN quantity
                ELSE 0
            END
        ) as total_purchase,

        SUM(
            CASE
                WHEN type = 'sale' AND direction = 'out'
                THEN ABS(quantity)
                ELSE 0
            END
        ) as total_sold,

        SUM(
            CASE
                WHEN type = 'sale_return'
                THEN ABS(quantity)

                WHEN type = 'purchase_return'
                THEN -ABS(quantity)

                ELSE 0
            END
        ) as total_return,

        SUM(quantity) as available_stock
    ")
    ->first();
$onlineOrderSold = DB::table('order_items')
    ->where('item_id', $itemId)
    ->selectRaw('SUM(qty) as total_online_sale')
    ->first();
/*
|--------------------------------------------------------------------------
| Summary values from batch-wise truth
|--------------------------------------------------------------------------
|
| Batch available_stock is final source of truth
|
*/


$totalReturn   = $summary->total_return ?? 0;
$totalPurchased = $summary->total_purchase ?? 0;
/*
|--------------------------------------------------------------------------
| Get valid batches only (exclude expired)
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Get valid batches + Correct Strip/Loose Stock
|--------------------------------------------------------------------------
*/

$batchesForStock = DB::table('batches')
    ->where('batches.item_id', $itemId)

    ->leftJoinSub(
        $this->stockMovementBatchSubQuery(),
        's',
        function ($join) {
            $join->on('batches.id', '=', 's.batch_id');
        }
    )

    ->leftJoinSub(
        $this->onlineOrderBatchSubQuery(),
        'o',
        function ($join) {
            $join->on('batches.id', '=', 'o.batch_id');
        }
    )

    ->select([
        'batches.id',
        'batches.batch_code',
        'batches.stock',
        'batches.loose_stock',

        DB::raw("
            (
                COALESCE(s.total_purchase, 0)
                - COALESCE(s.total_sale, 0)
                - COALESCE(o.total_online_sale, 0)
                + COALESCE(s.total_return, 0)
            ) as available_stock
        ")
    ])
    ->get();

/*
|--------------------------------------------------------------------------
| Final Available Stock
|--------------------------------------------------------------------------
*/
$isStripBased = in_array(strtolower($item->product_form), ['tablet','capsule']);

$packSize = $isStripBased ? ($item->pack_qty ?? 10) : 1;

$totalQty = 0;

foreach ($batchesForStock as $batch) {

    if ($isStripBased) {
        $strip = (int) ($batch->stock ?? 0);
        $loose = (int) ($batch->loose_stock ?? 0);

        $totalQty += ($strip * $packSize) + $loose;
    } else {
        $totalQty += (int) ($batch->available_stock ?? 0);
    }
}

// ✅ VERY IMPORTANT (fixes your error)
$finalAvailableStrip = 0;
$finalAvailableLoose = 0;
$finalAvailableUnit = 0;

if ($isStripBased) {
    $finalAvailableStrip = intdiv($totalQty, $packSize);
    $finalAvailableLoose = $totalQty % $packSize;
} else {
    $finalAvailableUnit = $totalQty;
}
/*
|--------------------------------------------------------------------------
| Sold = Purchase - Available + Return
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Convert everything into loose first
|--------------------------------------------------------------------------
*/


    /*
    |--------------------------------------------------------------------------
    | Batch-wise stock
    |--------------------------------------------------------------------------
    */
$batches = DB::table('batches')
    ->where('batches.item_id', $itemId)
    ->leftJoin('item_packings', 'batches.item_id', '=', 'item_packings.item_id')

    ->leftJoinSub(
        $this->stockMovementBatchSubQuery(),
        's',
        function ($join) {
            $join->on('batches.id', '=', 's.batch_id');
        }
    )

    ->leftJoinSub(
        $this->onlineOrderBatchSubQuery(),
        'o',
        function ($join) {
            $join->on('batches.id', '=', 'o.batch_id');
        }
    )

   ->select([
    'batches.id',
    'batches.item_id',
    'batches.batch_code',
    'batches.expiry_date',

    'batches.stock',
    'batches.loose_stock',
    'batches.mrp',
    'batches.ptr',
    'batches.selling_price',

    DB::raw('COALESCE(s.total_purchase, 0) as total_purchase'),
    DB::raw('COALESCE(s.total_sale, 0) as total_sale'),
    DB::raw('COALESCE(o.total_online_sale, 0) as total_online_sale'),
    DB::raw('COALESCE(s.total_return, 0) as total_return'),

DB::raw('
(
    COALESCE(s.total_purchase, 0)
    - COALESCE(s.total_sale, 0)
    - COALESCE(o.total_online_sale, 0)
    + COALESCE(s.total_return, 0)
) as available_stock
')
])

    ->orderBy('batches.expiry_date')
    ->get();    /*
    |--------------------------------------------------------------------------
    | Purchase History
    |--------------------------------------------------------------------------
    */

 $purchases = DB::table('purchase_items')
    ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
    ->leftJoin('batches', 'batches.id', '=', 'purchase_items.batch_id')
    ->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
    ->leftJoin('item_packings', 'item_packings.item_id', '=', 'purchase_items.item_id')

    ->where('purchase_items.item_id', $itemId)
->select([
    'purchase_items.id',
    'purchase_items.quantity',
    'purchase_items.free_quantity',
    

    DB::raw('0 as returned_quantity'),

    'purchase_items.ptr',
    'purchase_items.mrp',

    'purchase_items.gst_percent',
    'purchase_items.gst_amount',

    'purchase_items.discount_percent',
    'purchase_items.discount_amount',

    'purchase_items.taxable_amount',
    'purchase_items.total_amount',

    'purchases.invoice_number as purchase_bill_number',
    'purchases.created_at',

    'suppliers.name as supplier_name',

    'batches.batch_code as purchase_batch_code',
    'batches.expiry_date',
    'item_packings.packaging_detail',
'item_packings.product_form',
])
    ->orderBy('purchases.created_at', 'desc')
    ->get();  
    
    $purchaseReturns = DB::table('purchase_return_items')
    ->join('purchase_returns', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
    ->leftJoin('batches', 'batches.id', '=', 'purchase_return_items.batch_id')
    ->leftJoin('suppliers', 'suppliers.id', '=', 'purchase_returns.supplier_id')

    ->where('purchase_return_items.item_id', $itemId)

    ->select([
        'purchase_return_items.id',
        'purchase_return_items.quantity',
        'purchase_return_items.created_at',

        'batches.batch_code',

        'purchase_returns.id as return_id',
        'purchase_returns.total_amount',
        'purchase_returns.return_date',

        'suppliers.name as supplier_name',
    ])

    ->orderBy('purchase_return_items.created_at', 'desc')
    ->get();
    /*
    |--------------------------------------------------------------------------
    | Sales History
    |--------------------------------------------------------------------------
    */

$sales = DB::table('sales_items')
    ->join('sales', 'sales.id', '=', 'sales_items.sale_id')
    ->join('batches', 'batches.id', '=', 'sales_items.batch_id')
    ->leftJoin('customers', 'customers.id', '=', 'sales.customer_id')
    ->leftJoin('doctors', 'doctors.id', '=', 'sales.doctor_id')

    ->where('sales_items.item_id', $itemId)

    ->select([
        'sales.id as order_id',
        'sales.bill_number',
        'sales.created_at',

        'batches.batch_code',

        DB::raw('sales_items.quantity as qty'),
        'sales_items.sale_type',
'sales_items.unit_qty',
'sales_items.selling_price',

        DB::raw('(sales_items.quantity * sales_items.selling_price) as net_amount'),

        DB::raw("'Cash' as payment_type"),
        DB::raw("'Completed' as status"),

        'customers.name as customer_name',

        DB::raw("NULL as customer_mobile"),
        DB::raw("NULL as customer_email"),
        DB::raw("NULL as customer_address"),

        'doctors.name as doctor_name',
    ])

    ->orderBy('sales.created_at', 'desc')
    ->get();
    $totalSoldStrip = 0;
$totalSoldLoose = 0;

foreach ($sales as $sale) {

    // Strip Sale
    if ($sale->sale_type === 'strip') {
        $totalSoldStrip += (int) $sale->qty;
    }

    // Loose Sale
    if ($sale->sale_type === 'loose') {
        $totalSoldLoose += (int) ($sale->unit_qty ?? 0);
    }
}
$packSize = $item->pack_qty ?? 10;

// Step 1: convert strip into loose
$totalLooseFromStrip = $totalSoldStrip * $packSize;

// Step 2: total loose
$totalFinalLoose = $totalLooseFromStrip + $totalSoldLoose;

// Step 3: normalize back
$finalSoldStrip = intdiv($totalFinalLoose, $packSize);
$finalSoldLoose = $totalFinalLoose % $packSize;
    $onlineOrders = DB::table('order_items')
    ->join('orders', 'orders.id', '=', 'order_items.order_id')
    ->join('batches', 'batches.id', '=', 'order_items.batch_id')
    ->leftJoin('users', 'users.id', '=', 'orders.user_id')

    ->where('order_items.item_id', $itemId)

    ->select([
        'orders.id as order_id',
        'orders.created_at',
        'orders.status',
        'orders.payment_mode',
        'orders.total',

        'order_items.qty',
        'batches.batch_code',

        'users.name as customer_name',
        'users.email as customer_email',
DB::raw("NULL as customer_mobile"),
        DB::raw("NULL as doctor_name"),
        DB::raw("NULL as customer_address"),
        DB::raw("NULL as bill_number"),
    ])

    ->orderBy('orders.created_at', 'desc')
    ->get();
return view('stock.show', compact(
    'item',
    'totalReturn',
'totalPurchased',
    'totalSoldStrip',
    'totalSoldLoose',

    'finalSoldStrip',
    'finalSoldLoose',

    'finalAvailableStrip',
    'finalAvailableLoose',
     'finalAvailableUnit',

    'batches',
    'purchases',
    'purchaseReturns',
    'sales',
    'onlineOrders'
));

}
}