<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Batch;
use App\Models\Item;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\SupplierLedger;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        // ===============================
        // SALES (FIXED ✅)
        // ===============================
        $todaySales = Sale::whereDate('bill_date', $today)
            ->sum('net_amount');

        $monthlySales = Sale::whereBetween('bill_date', [$monthStart, $monthEnd])
            ->sum('net_amount');

        // ===============================
        // PURCHASE
        // ===============================
        $todayPurchase = Purchase::whereDate('created_at', $today)
            ->sum('net_amount');

        // ===============================
        // CUSTOMER OUTSTANDING
        // ===============================
        $customerOutstanding = CustomerLedger::orderBy('id', 'desc')
            ->get()
            ->unique('customer_id')
            ->sum('balance');

        // ===============================
        // SUPPLIER PAYABLE
        // ===============================
        $supplierPayable = SupplierLedger::orderBy('id', 'desc')
            ->get()
            ->unique('supplier_id')
            ->sum('balance');

        // ===============================
        // LOW STOCK
        // ===============================
        $lowStockCount = Batch::where('stock', '<', 10)
            ->where('stock', '>', 0)
            ->count();

        // ===============================
        // EXPIRY ALERT
        // ===============================
        $expiryNearCount = Batch::whereDate('expiry_date', '<=', now()->addDays(30))
            ->where('stock', '>', 0)
            ->count();

        // ===============================
        // TOTAL COUNTS
        // ===============================
        $totalMedicines = Item::count();
        $totalCustomers = Customer::count();

        return view('dashboard', compact(
            'todaySales',
            'monthlySales',
            'todayPurchase',
            'customerOutstanding',
            'supplierPayable',
            'lowStockCount',
            'expiryNearCount',
            'totalMedicines',
            'totalCustomers'
        ));
    }
}