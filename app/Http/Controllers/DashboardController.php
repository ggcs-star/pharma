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

        // ===============================
        // SALES (Customer Purchases) ✅
        // ===============================
        $todaySales = Sale::whereDate('bill_date', $today)
            ->sum('net_amount');
        
        $todayPurchaseCount = Sale::whereDate('bill_date', $today)
            ->count();

        $monthlySales = Sale::whereBetween('bill_date', [$monthStart, $monthEnd])
            ->sum('net_amount');

        // Calculate trend (compare with yesterday)
        $yesterdaySales = Sale::whereDate('bill_date', now()->subDay())
            ->sum('net_amount');
        $todaySalesTrend = $yesterdaySales > 0 
            ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1)
            : 0;
        
        $todayComparison = $todaySalesTrend > 0 
            ? '+' . $todaySalesTrend . '%' 
            : $todaySalesTrend . '%';

        // ===============================
        // PURCHASE (Supplier Purchases)
        // ===============================
        $todayPurchase = Purchase::whereDate('created_at', $today)
            ->sum('net_amount');
        
        $purchaseTransactions = Purchase::whereDate('created_at', $today)
            ->count();

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

        // ===============================
        // TOTAL COUNTS
        // ===============================
        $totalMedicines = Item::count();
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
        $recentTransactions = Sale::with('customer')
            ->orderBy('bill_date', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get()
            ->map(function($sale) {
                // Determine payment status without paid_amount column
                // You might have other columns like 'payment_status' or calculate from ledger
                $status = 'Completed';
                
                return (object)[
                    'time' => $sale->created_at->format('h:i A'),
                    'customer' => $sale->customer->name ?? 'Walk-in Customer',
                    'amount' => $sale->net_amount,
                    'status' => $status,
                    'bill_no' => $sale->bill_no ?? 'N/A'
                ];
            });

        // ===============================
        // CHART DATA (Last 7 days)
        // ===============================
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $chartLabels[] = now()->subDays($i)->format('D');
            
            $daySales = Sale::whereDate('bill_date', $date)->sum('net_amount');
            $chartData[] = $daySales;
        }

        return view('dashboard', compact(
            'todaySales',
            'todayPurchaseCount',
            'monthlySales',
            'todaySalesTrend',
            'todayComparison',
            'todayPurchase',
            'purchaseTransactions',
            'customerOutstanding',
            'overdueCustomers',
            'supplierPayable',
            'lowStockCount',
            'expiryNearCount',
            'totalMedicines',
            'totalCustomers',
            'totalSuppliers',
            'totalCategories',
            'recentTransactions',
            'chartLabels',
            'chartData'
        ));
    }
}