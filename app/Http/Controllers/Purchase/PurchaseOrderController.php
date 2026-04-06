<?php


namespace App\Http\Controllers\Purchase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Purchase\PurchaseOrderItem;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Batch;
use App\Models\Supplier;
use App\Models\Item;

class PurchaseOrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */
   public function index(Request $request)
{
    $query = PurchaseOrder::with('supplier');

    // 🔍 DATE FILTER
    if ($request->from_date && $request->to_date) {
        $query->whereBetween('order_date', [
            $request->from_date,
            $request->to_date
        ]);
    }

    // 🔍 SEARCH
    if ($request->search) {
        $query->where('order_number', 'like', '%' . $request->search . '%');
    }

    $orders = $query->latest()->paginate(10);

    return view('purchase_orders.index', compact('orders'));
}

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $suppliers = Supplier::all();
        $items = Item::select('id','name','gst_percent')->get();

        return view('purchase_orders.create', compact('suppliers','items'));
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */
public function store(Request $request)
{
    $request->validate([
        'supplier_id' => 'required|exists:suppliers,id',
        'order_date' => 'required|date',
        'items' => 'required|array|min:1',
        'items.*.item_id' => 'required|exists:items,id',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'items.*.rate' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {

        // 🔥 BETTER ORDER NUMBER
        $orderNumber = 'PO-' . date('Ymd') . '-' . rand(1000,9999);

        $order = PurchaseOrder::create([
            'supplier_id' => $request->supplier_id,
            'order_number' => $orderNumber,
            'order_date' => $request->order_date,
            'status' => 'draft',
            'total_amount' => 0,
            'total_gst' => 0,
            'total_discount' => 0,
            'net_amount' => 0
        ]);

        $totalAmount = 0;
        $totalGST = 0;
        $totalDiscount = 0;

        foreach ($request->items as $item) {

            // ❌ EMPTY ROW SKIP (VERY IMPORTANT)
            if (!$item['item_id'] || !$item['quantity']) continue;

            $qty = $item['quantity'];
            $rate = $item['rate'];

            $basic = $qty * $rate;

            $discountPercent = $item['discount_percent'] ?? 0;
            $discount = ($basic * $discountPercent) / 100;

            $taxable = $basic - $discount;

            $gstPercent = $item['gst_percent'] ?? 0;
            $gst = ($taxable * $gstPercent) / 100;

            $total = $taxable + $gst;

            PurchaseOrderItem::create([
                'purchase_order_id' => $order->id,
                'item_id' => $item['item_id'],
                'quantity' => $qty,
                'rate' => $rate,
                'gst_percent' => $gstPercent,
                'gst_amount' => $gst,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discount,
                'taxable_amount' => $taxable,
                'total_amount' => $total
            ]);

            $totalAmount += $basic;
            $totalGST += $gst;
            $totalDiscount += $discount;
        }

        $order->update([
            'total_amount' => $totalAmount,
            'total_gst' => $totalGST,
            'total_discount' => $totalDiscount,
            'net_amount' => ($totalAmount - $totalDiscount + $totalGST)
        ]);

        DB::commit();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order created successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}
    /*
    |--------------------------------------------------------------------------
    | Convert PO → Purchase
    |--------------------------------------------------------------------------
    */
   public function convert($id)
{
    $order = PurchaseOrder::with('items.item')->findOrFail($id);

    return redirect()->route('purchase.create', [
        'po_id' => $order->id
    ]);
}
}