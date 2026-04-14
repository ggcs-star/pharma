<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /* ===============================
        PLACE ORDER
    =============================== */
public function place(Request $request)
{
    DB::beginTransaction();

    try {

        // ✅ STRICT VALIDATION
        if (!$request->payment_mode) {
            throw new \Exception('Payment mode missing');
        }

        if ($request->payment_mode == 'razorpay' && !$request->payment_id) {
            throw new \Exception('Payment not completed');
        }

        // DEBUG (temporary)
        \Log::info($request->all());

$cartItems = Cart::with(['batch', 'item'])
    ->where('user_id', auth()->id()) // 🔥 FIX
    ->lockForUpdate()
    ->get();
$requiresPrescription = $cartItems->contains(function($c){
    return optional($c->item)->need_prescription == 1;
});

if ($requiresPrescription && !$request->prescription_id) {
    throw new \Exception('Prescription required');
}

if ($cartItems->isEmpty()) {
    throw new \Exception('Cart is empty');
}
if ($requiresPrescription && !$request->prescription_id) {
    throw new \Exception('Prescription required');
}
        if ($cartItems->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        $total = 0;
// 🔥 VALIDATE PRESCRIPTION BELONGS TO USER
if ($request->prescription_id) {
    $prescription = \App\Models\Prescription::where('id', $request->prescription_id)
        ->where('user_id', auth()->id())
        ->first();

    if (!$prescription) {
        throw new \Exception('Invalid prescription');
    }
}
        // ✅ CREATE ORDER (NO DEFAULT COD)
      $order = Order::create([
    'user_id' => auth()->id(),
    'address_id' => $request->address_id,
    'prescription_id' => $request->prescription_id, // 🔥 ADD THIS
    'status' => 'pending_approval', // 🔥 CHANGE THIS
    'total' => 0,
    'payment_id' => $request->payment_id ?? null,
    'payment_mode' => $request->payment_mode,
    'payment_status' => $request->payment_mode == 'razorpay' ? 'paid' : 'pending'
]);

        foreach ($cartItems as $cart) {

            $batch = Batch::lockForUpdate()->findOrFail($cart->batch_id);

            if ($batch->stock < $cart->qty) {
                throw new \Exception("Stock issue");
            }

            $price = $cart->price > 0 
                ? $cart->price 
                : ($batch->sale_price ?? $batch->mrp ?? 0);

            $lineTotal = $price * $cart->qty;

            OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $cart->item_id,
                'batch_id' => $cart->batch_id,
                'qty' => $cart->qty,
                'price' => $price,
                'total' => $lineTotal
            ]);

            $batch->decrement('stock', $cart->qty);

            $total += $lineTotal;
        }

        
$order->update([
    'total' => $total,
    'status' => 'pending_approval' // 🔥 ALWAYS THIS
]);    

        Cart::where('user_id', auth()->id())->delete();

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Order placed successfully',
            'order_id' => $order->id
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}
    /* ===============================
        ORDER LIST
    =============================== */
    public function index()
    {
        $orders = Order::with('items')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }


    /* ===============================
        ORDER DETAIL
    =============================== */
 public function show($id)
{
    $order = Order::with([
        'user',
        'address',
        'items.batch',
        'items.item'
    ])
    ->where('user_id', auth()->id())
    ->findOrFail($id);

    // ✅ ADDRESS MAPPING
    if ($order->address) {
        $order->address->line1 = $order->address->address_line_1;
        $order->address->line2 = $order->address->address_line_2;
        $order->address->mobile = $order->address->phone;
    }

    // ✅ USER MOBILE FIX
    if ($order->user) {
        $order->user->mobile =
            $order->user->mobile
            ?? $order->user->phone
            ?? $order->user->mobile_no
            ?? null;
    }

    return response()->json([
        'status' => true,
        'data' => $order
    ]);
}


    /* ===============================
        CANCEL ORDER
    =============================== */
    public function cancel($id)
    {
        DB::beginTransaction();

        try {

            $order = Order::with('items')
                ->where('user_id', auth()->id())
                ->lockForUpdate()
                ->findOrFail($id);

            if (!in_array($order->status, ['pending', 'pending_approval'])) {
                throw new \Exception('Only pending orders can be cancelled');
            }

            foreach ($order->items as $item) {

                $batch = Batch::lockForUpdate()->find($item->batch_id);

                if ($batch) {
                    $batch->increment('stock', $item->qty);
                }
            }

            $order->update([
                'status' => 'cancelled'
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order cancelled successfully'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}