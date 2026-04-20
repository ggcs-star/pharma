<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseItem;

use Illuminate\Support\Facades\DB;
class StockController extends Controller
{



    public function index(Request $request)
    {
        $search = $request->search;

        $stocks = $this->getItemStock($search);
        $batchStocks = $this->getBatchStock();

        return view('stock.index', compact('stocks', 'batchStocks'));
    }

    public function show($itemId)
    {

        $item = DB::table('items')
            ->where('id', $itemId)
            ->first();

        $summary = DB::table('purchase_items')
            ->where('item_id', $itemId)
            ->selectRaw('
            SUM(quantity + COALESCE(free_quantity,0)) as total_purchase,
            SUM(COALESCE(returned_quantity,0)) as total_return
        ')
            ->first();

        $sold = DB::table('order_items')
            ->where('item_id', $itemId)
            ->selectRaw('SUM(qty) as total_sold')
            ->first();

        $totalPurchase = $summary->total_purchase ?? 0;
        $totalReturn = $summary->total_return ?? 0;
        $totalSold = $sold->total_sold ?? 0;

        $available = $totalPurchase - $totalReturn - $totalSold;

        $batches = DB::table('batches')
            ->where('item_id', $itemId)

            ->leftJoinSub($this->purchaseBatchSubQuery(), 'p', function ($join) {
                $join->on('batches.id', '=', 'p.batch_id');
            })

            ->leftJoinSub($this->orderBatchSubQuery(), 'o', function ($join) {
                $join->on('batches.id', '=', 'o.batch_id');
            })

            ->select([
                'batches.batch_code',
                'batches.expiry_date',

                DB::raw('COALESCE(p.total_purchase,0) as total_purchase'),
                DB::raw('COALESCE(p.total_return,0) as total_return'),
                DB::raw('COALESCE(o.total_sold,0) as total_sold'),

                DB::raw('
                (COALESCE(p.total_purchase,0)
                - COALESCE(p.total_return,0)
                - COALESCE(o.total_sold,0)) as available_stock
            ')
            ])
            ->orderBy('batches.expiry_date')
            ->get();
        $purchases = DB::table('purchase_items')
            ->join('batches', 'batches.id', '=', 'purchase_items.batch_id')
            ->where('purchase_items.item_id', $itemId)
            ->select([
                'purchase_items.quantity',
                'purchase_items.free_quantity',
                'purchase_items.returned_quantity',
                'batches.batch_code',
                'purchase_items.created_at'
            ])
            ->orderBy('purchase_items.created_at', 'desc')
            ->get();
        $sales = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('batches', 'batches.id', '=', 'order_items.batch_id')
            ->where('order_items.item_id', $itemId)
            ->select([
                'order_items.qty',
                'batches.batch_code',
                'orders.id as order_id',
                'order_items.created_at'
            ])
            ->orderBy('order_items.created_at', 'desc')
            ->get();
        return view('stock.show', compact(
            'item',
            'totalPurchase',
            'totalSold',
            'available',
            'batches',
            'purchases',
            'sales'
        ));
    }

    private function getItemStock($search = null)
    {
        return DB::table('items')


            ->whereIn('items.id', function ($q) {
                $q->select('item_id')->from('purchase_items');
            })

            ->when($search, function ($q) use ($search) {
                $q->where('items.name', 'like', "%{$search}%");
            })

            ->leftJoinSub($this->purchaseItemSubQuery(), 'p', function ($join) {
                $join->on('items.id', '=', 'p.item_id');
            })

            ->leftJoinSub($this->orderItemSubQuery(), 'o', function ($join) {
                $join->on('items.id', '=', 'o.item_id');
            })

            ->select([
                'items.id',
                'items.name',

                DB::raw('COALESCE(p.total_purchase, 0) as total_purchase'),
                DB::raw('COALESCE(p.total_return, 0) as total_return'),
                DB::raw('COALESCE(o.total_sold, 0) as total_sold'),

                DB::raw('
                (COALESCE(p.total_purchase,0)
                - COALESCE(p.total_return,0)
                - COALESCE(o.total_sold,0)) as available_stock
            ')
            ])

            ->orderBy('items.name')
            ->paginate(10);
    }

    private function getBatchStock()
    {
        return DB::table('batches')

            ->leftJoinSub($this->purchaseBatchSubQuery(), 'p', function ($join) {
                $join->on('batches.id', '=', 'p.batch_id');
            })

            ->leftJoinSub($this->orderBatchSubQuery(), 'o', function ($join) {
                $join->on('batches.id', '=', 'o.batch_id');
            })

            ->select([
                'batches.id',
                'batches.item_id',
                'batches.batch_code',
                'batches.expiry_date',

                DB::raw('COALESCE(p.total_purchase, 0) as total_purchase'),
                DB::raw('COALESCE(p.total_return, 0) as total_return'),
                DB::raw('COALESCE(o.total_sold, 0) as total_sold'),

                DB::raw('
                (COALESCE(p.total_purchase,0)
                - COALESCE(p.total_return,0)
                - COALESCE(o.total_sold,0)) as available_stock
            ')
            ])

            ->orderBy('batches.expiry_date')
            ->get()
            ->groupBy('item_id');
    }

    private function purchaseItemSubQuery()
    {
        return DB::table('purchase_items')
            ->select(
                'item_id',
                DB::raw('SUM(quantity + COALESCE(free_quantity,0)) as total_purchase'),
                DB::raw('SUM(COALESCE(returned_quantity,0)) as total_return')
            )
            ->groupBy('item_id');
    }

    private function orderItemSubQuery()
    {
        return DB::table('order_items')
            ->select(
                'item_id',
                DB::raw('SUM(qty) as total_sold')
            )
            ->groupBy('item_id');
    }

    private function purchaseBatchSubQuery()
    {
        return DB::table('purchase_items')
            ->select(
                'batch_id',
                DB::raw('SUM(quantity + COALESCE(free_quantity,0)) as total_purchase'),
                DB::raw('SUM(COALESCE(returned_quantity,0)) as total_return')
            )
            ->groupBy('batch_id');
    }
    private function orderBatchSubQuery()
    {
        return DB::table('order_items')
            ->select(
                'batch_id',
                DB::raw('SUM(qty) as total_sold')
            )
            ->groupBy('batch_id');
    }
}