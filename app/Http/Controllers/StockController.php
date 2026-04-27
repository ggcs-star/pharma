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

    return view('stock.index', compact('stocks', 'batchStocks'));
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
    $q->whereIn('id', function ($sub) {
        $sub->select('item_id')
            ->from('stock_movements');
    })
    ->orWhereIn('id', function ($sub) {
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

        /*
        |--------------------------------------------------------------------------
        | Final Select
        |--------------------------------------------------------------------------
        */

        ->select([
            'items.*',

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

        ->select([
            'batches.id',
            'batches.item_id',
            'batches.batch_code',
            'batches.expiry_date',

            DB::raw('COALESCE(s.total_purchase, 0) as total_purchase'),
            DB::raw('COALESCE(s.total_sale, 0) as total_sale'),
            DB::raw('COALESCE(s.total_return, 0) as total_return'),
            DB::raw('COALESCE(s.available_stock, 0) as available_stock'),
        ])

        ->orderBy('batches.expiry_date')
        ->get()
        ->groupBy('item_id');
}

    






public function show($itemId)
{
    $item = DB::table('items')
        ->where('id', $itemId)
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

    $totalPurchase = $summary->total_purchase ?? 0;
    $totalReturn = $summary->total_return ?? 0;
    $totalSold = $summary->total_sold ?? 0;
    $available = $summary->available_stock ?? 0;

    /*
    |--------------------------------------------------------------------------
    | Batch-wise stock
    |--------------------------------------------------------------------------
    */

    $batches = DB::table('batches')
        ->where('batches.item_id', $itemId)

        ->leftJoinSub(
            $this->stockMovementBatchSubQuery(),
            's',
            function ($join) {
                $join->on('batches.id', '=', 's.batch_id');
            }
        )

        ->select([
            'batches.batch_code',
            'batches.expiry_date',

            DB::raw('COALESCE(s.total_purchase, 0) as total_purchase'),
DB::raw('COALESCE(s.total_sale, 0) as total_sale'),
            DB::raw('COALESCE(s.total_return, 0) as total_return'),
            DB::raw('COALESCE(s.available_stock, 0) as available_stock'),
        ])

        ->orderBy('batches.expiry_date')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Purchase History
    |--------------------------------------------------------------------------
    */

  $purchases = DB::table('stock_movements')
    ->join('batches', 'batches.id', '=', 'stock_movements.batch_id')
    ->where('stock_movements.item_id', $itemId)
    ->where('stock_movements.type', 'purchase')

    ->select([
        'stock_movements.quantity',
        'stock_movements.created_at',
        'batches.batch_code',

        DB::raw('0 as free_quantity'),
        DB::raw('0 as returned_quantity'),
    ])

    ->orderBy('stock_movements.created_at', 'desc')
    ->get();
    /*
    |--------------------------------------------------------------------------
    | Sales History
    |--------------------------------------------------------------------------
    */

   $sales = DB::table('stock_movements')
    ->join('batches', 'batches.id', '=', 'stock_movements.batch_id')
    ->leftJoin('sales', 'sales.id', '=', 'stock_movements.reference_id')
    ->leftJoin('customers', 'customers.id', '=', 'sales.customer_id')
    ->leftJoin('doctors', 'doctors.id', '=', 'sales.doctor_id')

    ->where('stock_movements.item_id', $itemId)
    ->where('stock_movements.type', 'sale')

    ->select([
        DB::raw('ABS(stock_movements.quantity) as qty'),

        'batches.batch_code',

        'stock_movements.reference_id as order_id',
        'stock_movements.created_at',

        'sales.bill_number',
       DB::raw("'Cash' as payment_type"),
DB::raw("0 as net_amount"),
DB::raw("'Completed' as status"),
        'customers.name as customer_name',
    DB::raw("NULL as customer_mobile"),
DB::raw("NULL as customer_email"),
DB::raw("NULL as customer_address"),
        'doctors.name as doctor_name',
    ])

    ->orderBy('stock_movements.created_at', 'desc')
    ->get();
 return view('stock.show', compact(
    'item',
    'totalPurchase',
    'totalReturn',
    'totalSold',
    'available',
    'batches',
    'purchases',
    'sales'
));
}
}