<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\Batch;
use App\Models\Item;
use App\Models\Customer;
use App\Models\Doctor;
use App\Models\CustomerLedger;
use App\Models\StockMovement;

class SalesController extends Controller
{

    // =========================
    // SALES LIST
    // =========================
    public function index(Request $request)
    {
        $sales = Sale::with(['items.item', 'customer', 'doctor'])
            ->when($request->search, function ($query, $search) {
                $query->where('bill_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(20);

        return view('sales.index', compact('sales'));
    }

    // =========================
    // CREATE PAGE
    // =========================
    public function create()
    {
        $items = Item::whereHas('batches', function ($q) {
            $q->where('stock', '>', 0)
              ->whereDate('expiry_date', '>=', now());
        })->get();

        $customers = Customer::orderBy('name')->get();
        $doctors   = Doctor::orderBy('name')->get();

        return view('sales.create', compact('items', 'customers', 'doctors'));
    }

    // =========================
    // STORE SALE
    // =========================
   public function store(Request $request)
{
    $validated = $request->validate([
        'bill_date' => 'required|date',
        'customer_id' => 'nullable|exists:customers,id',
        'doctor_id' => 'nullable|exists:doctors,id',
        'payment_type' => 'required',
        'items' => 'required|array|min:1',

        'items.*.item_id' => 'required|exists:items,id',
        'items.*.batch_id' => 'required|exists:batches,id',
        'items.*.qty' => 'required|numeric|min:1',
        'items.*.selling_price' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {

        // 🔥 AUTO BILL NUMBER
        $billNumber = $this->generateBillNumber();

        $subtotal = 0;
        $totalGST = 0;
        $grandTotal = 0;

        // =========================
        // CREATE SALE
        // =========================
        $sale = Sale::create([
            'bill_number' => $billNumber,
            'bill_date' => $validated['bill_date'],
            'customer_id' => $validated['customer_id'] ?? null,
            'doctor_id' => $validated['doctor_id'] ?? null,
            'payment_type' => $validated['payment_type'],
            'entry_by' => Auth::id(),
            'status' => 'completed'
        ]);

        $itemsData = [];

        foreach ($validated['items'] as $row) {

            $item = Item::findOrFail($row['item_id']);
            $batch = Batch::findOrFail($row['batch_id']);

            // ❗ STOCK CHECK
            if ($batch->stock < $row['qty']) {
                throw new \Exception("Stock not available for {$item->name}");
            }

            $base = $row['qty'] * $row['selling_price'];

            $gstPercent = $item->gst_percent ?? 0;
            $gstAmount = ($base * $gstPercent) / 100;

            $final = $base + $gstAmount;

            $itemsData[] = [
                'sale_id' => $sale->id,
                'item_id' => $item->id,
                'batch_id' => $batch->id,
                'quantity' => $row['qty'],
                'selling_price' => $row['selling_price'],
                'mrp' => $batch->mrp,
                'discount' => 0,
                'gst' => $gstPercent,
                'gst_amount' => $gstAmount,
                'amount' => $final,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // 🔻 STOCK CUT
            $batch->decrement('stock', $row['qty']);

            // 📦 STOCK MOVEMENT
            StockMovement::create([
                'item_id' => $item->id,
                'batch_id' => $batch->id,
                'type' => 'sale',
                'quantity' => -$row['qty'],
                'running_stock' => $batch->stock,
                'reference_id' => $sale->id,
                'reference_type' => Sale::class,
                'user_id' => Auth::id(),
                'transaction_date' => now()
            ]);

            $subtotal += $base;
            $totalGST += $gstAmount;
            $grandTotal += $final;
        }

        // 💾 SAVE ITEMS
        SalesItem::insert($itemsData);

        // 💰 UPDATE TOTAL
        $sale->update([
            'total_amount' => $subtotal,
            'gst' => $totalGST,
            'net_amount' => $grandTotal
        ]);

        // =========================
        // 📘 CUSTOMER LEDGER (FIXED ✅)
        // =========================
        if (
            $validated['payment_type'] == 'credit' &&
            !empty($validated['customer_id'])
        ) {

            $lastBalance = CustomerLedger::where('customer_id', $validated['customer_id'])
                ->latest()
                ->value('balance') ?? 0;

            $newBalance = $lastBalance + $grandTotal;

            CustomerLedger::create([
                'customer_id' => $validated['customer_id'],
                'reference_id' => $sale->id,
                'type' => 'sale', // 🔥 IMPORTANT
                'debit' => $grandTotal,
                'credit' => 0,
                'balance' => $newBalance,
                'transaction_date' => now(),
                'description' => 'Sale #' . $sale->bill_number,
                'created_by' => Auth::id(),
            ]);
        }

        DB::commit();

        return redirect()->route('sales.index')
            ->with('success', 'Sale Saved Successfully');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->with('error', $e->getMessage());
    }
}
    // =========================
    // SHOW SALE
    // =========================
    public function show($id)
    {
        $sale = Sale::with(['items.item', 'items.batch', 'customer', 'doctor'])
            ->findOrFail($id);

        return view('sales.show', compact('sale'));
    }

    // =========================
    // AUTO BILL NUMBER
    // =========================
    private function generateBillNumber()
    {
        return DB::transaction(function () {

            $last = Sale::lockForUpdate()
                ->orderByDesc('id')
                ->first();

            if (!$last || !$last->bill_number) {
                return 'SAL-0001';
            }

            $number = (int) preg_replace('/[^0-9]/', '', $last->bill_number);
            $next = $number + 1;

            return 'SAL-' . str_pad($next, 4, '0', STR_PAD_LEFT);
        });
    }
}