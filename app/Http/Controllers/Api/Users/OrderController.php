<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Batch;
use App\Services\SecurityLogger;
use Illuminate\Support\Facades\Log;
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

    if (!$request->payment_mode) {
        throw new \Exception('Payment mode missing');
    }

    if (
        $request->payment_mode === 'razorpay' &&
        !$request->payment_id
    ) {
        throw new \Exception('Payment not completed');
    }

    $cartItems = Cart::with(['batch', 'item'])
        ->where('user_id', auth()->id())
        ->lockForUpdate()
        ->get();

    if ($cartItems->isEmpty()) {
        throw new \Exception('Cart is empty');
    }

    $requiresPrescription = $cartItems->contains(function ($c) {
        return optional($c->item)->need_prescription == 1;
    });

    if (
        $requiresPrescription &&
        !$request->prescription_id
    ) {
        throw new \Exception('Prescription required');
    }

    if ($request->prescription_id) {

        $prescription = \App\Models\Prescription::where(
            'id',
            $request->prescription_id
        )
        ->where(
            'user_id',
            auth()->id()
        )
        ->first();

        if (!$prescription) {
            throw new \Exception(
                'Invalid prescription'
            );
        }
    }

    $total = 0;

    $order = Order::create([
        'user_id' => auth()->id(),
        'address_id' => $request->address_id,
        'prescription_id' => $request->prescription_id,
        'status' => 'pending_approval',
        'total' => 0,
        'payment_id' => $request->payment_id,
        'payment_mode' => $request->payment_mode,
        'payment_status' =>
            $request->payment_mode === 'razorpay'
                ? 'paid'
                : 'pending'
    ]);

    foreach ($cartItems as $cart) {

        $batch = Batch::lockForUpdate()
            ->findOrFail($cart->batch_id);

        if ($batch->stock < $cart->qty) {
            throw new \Exception(
                'Stock issue'
            );
        }

        $price = $cart->price > 0
            ? $cart->price
            : (
                $batch->sale_price
                ?? $batch->mrp
                ?? 0
            );

        $lineTotal = $price * $cart->qty;

        OrderItem::create([
            'order_id' => $order->id,
            'item_id' => $cart->item_id,
            'batch_id' => $cart->batch_id,
            'qty' => $cart->qty,
            'price' => $price,
            'total' => $lineTotal
        ]);

        $batch->decrement(
            'stock',
            $cart->qty
        );

        $total += $lineTotal;
    }

    $order->update([
        'total' => $total,
        'status' => 'pending_approval'
    ]);

    Cart::where(
        'user_id',
        auth()->id()
    )->delete();

    DB::commit();

    SecurityLogger::log(
        $request,
        'ORDER',
        'order_created',
        3,
        200,
        [
            'order_id' => $order->id,
            'amount' => $total,
            'payment_mode' => $request->payment_mode,
            'payment_status' => $order->payment_status,
            'items_count' => $cartItems->count(),
            'prescription_id' => $request->prescription_id
        ]
    );

    return response()->json([
        'status' => true,
        'message' => 'Order placed successfully',
        'order_id' => $order->id
    ]);

} catch (\Exception $e) {

    DB::rollBack();

    Log::error(
        'Order Create Error',
        [
            'message' => $e->getMessage()
        ]
    );

    SecurityLogger::log(
        $request,
        'ORDER',
        'order_create_failed',
        6,
        400,
        [
            'error' => $e->getMessage()
        ]
    );

    return response()->json([
        'status' => false,
        'message' => $e->getMessage()
    ], 400);
}

}    /* ===============================
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
public function cancel($id, Request $request)
{
DB::beginTransaction();

try {

    $order = Order::with('items')
        ->where('user_id', auth()->id())
        ->lockForUpdate()
        ->findOrFail($id);

    if (
        !in_array(
            $order->status,
            ['pending', 'pending_approval']
        )
    ) {
        throw new \Exception(
            'Only pending orders can be cancelled'
        );
    }

    foreach ($order->items as $item) {

        $batch = Batch::lockForUpdate()
            ->find($item->batch_id);

        if ($batch) {
            $batch->increment(
                'stock',
                $item->qty
            );
        }
    }

    $order->update([
        'status' => 'cancelled'
    ]);

    SecurityLogger::log(
        $request,
        'ORDER',
        'order_cancelled',
        4,
        200,
        [
            'order_id' => $order->id,
            'status' => 'cancelled',
            'user_id' => auth()->id()
        ]
    );

    DB::commit();

    return response()->json([
        'status' => true,
        'message' => 'Order cancelled successfully'
    ]);

} catch (\Exception $e) {

    DB::rollBack();

    Log::error(
        'Order Cancel Error',
        [
            'order_id' => $id,
            'message' => $e->getMessage()
        ]
    );

    SecurityLogger::log(
        $request,
        'ORDER',
        'order_cancel_failed',
        6,
        400,
        [
            'order_id' => $id,
            'error' => $e->getMessage()
        ]
    );

    return response()->json([
        'status' => false,
        'message' => $e->getMessage()
    ], 400);
}

}
}