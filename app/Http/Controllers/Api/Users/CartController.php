<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /* ===============================
        ADD TO CART
    =============================== */
    public function add(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'qty' => 'required|numeric|min:1'
        ]);

        DB::beginTransaction();

        try {
            $batch = Batch::lockForUpdate()->findOrFail($request->batch_id);

            if ($batch->expiry_date <= now()) {
                throw new \Exception('Product expired');
            }

            if ($batch->stock < $request->qty) {
                throw new \Exception('Only ' . $batch->stock . ' available');
            }

            // ✅ PRICE LOGIC
         $price = $batch->mrp ?? 0;

            $cart = Cart::where('user_id', auth()->id())
                ->where('batch_id', $batch->id)
                ->first();

            if ($cart) {
                $newQty = $cart->qty + $request->qty;

                if ($batch->stock < $newQty) {
                    throw new \Exception('Max quantity: ' . $batch->stock);
                }

                $cart->update([
                    'qty' => $newQty,
                    'price' => $price
                ]);

                $message = 'Cart updated';
            } else {
                $cart = Cart::create([
                    'user_id' => auth()->id(),
                    'item_id' => $batch->item_id,
                    'batch_id' => $batch->id,
                    'qty' => $request->qty,
                    'price' => $price
                ]);

                $message = 'Added to cart';
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $this->formatItem($cart),
                'summary' => $this->summary()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Cart Add Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /* ===============================
        CART LIST
    =============================== */
 public function index()
{
    
    $cartItems = Cart::with(['item','batch'])
        ->where('user_id', auth()->id())
        ->get();

    // 🔥 DEBUG
    foreach ($cartItems as $c) {
        if (!$c->item) {
            dd("ITEM RELATION NULL", $c);
        }
    }

    $cartItems = $cartItems
        ->map(fn($c) => $this->formatItem($c))
        ->values();

    return response()->json([
        'status' => true,
        'items' => $cartItems,
        'summary' => $this->summary()
    ]);
}

    /* ===============================
        UPDATE QTY
    =============================== */
    public function update(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'qty' => 'required|numeric|min:1'
        ]);

        DB::beginTransaction();

        try {
            $cart = Cart::where('id', $request->cart_id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $batch = Batch::lockForUpdate()->findOrFail($cart->batch_id);

            if ($batch->expiry_date <= now()) {
                throw new \Exception('Product expired');
            }

            if ($batch->stock < $request->qty) {
                throw new \Exception('Only ' . $batch->stock . ' available');
            }

            // ✅ PRICE SYNC
          $price = $batch->mrp ?? 0;

            $cart->update([
                'qty' => $request->qty,
                'price' => $price
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Quantity updated',
                'data' => $this->formatItem($cart),
                'summary' => $this->summary()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Cart Update Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /* ===============================
        REMOVE ITEM
    =============================== */
    public function remove($id)
    {
        try {
            $cart = Cart::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $cart->delete();

            return response()->json([
                'status' => true,
                'message' => 'Item removed',
                'summary' => $this->summary()
            ]);

        } catch (\Exception $e) {
            Log::error('Cart Remove Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Item not found'
            ], 404);
        }
    }

    /* ===============================
        CLEAR CART
    =============================== */
    public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();

        return response()->json([
            'status' => true,
            'message' => 'Cart cleared',
            'summary' => [
                'items' => 0,
                'qty' => 0,
                'total' => 0
            ]
        ]);
    }

    /* ===============================
        SUMMARY
    =============================== */
    private function summary()
    {
        $cart = Cart::where('user_id', auth()->id())->get();

        return [
            'items' => $cart->count(),
            'qty' => $cart->sum('qty'),
            'total' => $cart->sum(fn($c) => $c->qty * $c->price)
        ];
    }

    /* ===============================
        FORMAT ITEM (🔥 FINAL FIX)
    =============================== */
private function formatItem($c)
{
    $price = $c->batch->mrp ?? 0;

    $image = !empty($c->item->main_image)
        ? "https://pharma-catalog-assets.s3.us-east-1.amazonaws.com/".$c->item->main_image
        : asset('no-image.png');

    return [
        'id' => $c->id,
        'batch_id' => $c->batch_id,
        'name' => $c->item->name ?? 'Product',
        'main_image' => $image,
        'price' => $price,
        'qty' => $c->qty,
        'total_price' => $c->qty * $price,
        'stock' => $c->batch->stock ?? 0,
        'expiry' => $c->batch->expiry_date ?? null,

        // 🔥 ADD THIS LINE
        'need_prescription' => $c->item->need_prescription ?? 0
    ];
}

}