<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerLedger;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerLedgerController extends Controller
{
    /**
     * 🔹 Ledger List + Filter + Totals
     */
    public function index(Request $request)
    {
        $customers = Customer::all();

        $query = CustomerLedger::with('customer');

        // 🔍 Filter by customer
        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        // 📅 Date filter
        if ($request->from_date) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('transaction_date', '<=', $request->to_date);
        }

        // 📊 Get data
        $ledgers = $query->orderBy('id')->get();

        // 🔥 Totals
        $totalDebit = $ledgers->sum('debit');
        $totalCredit = $ledgers->sum('credit');
        $closingBalance = $ledgers->last()->balance ?? 0;

        return view('ledger.customer_index', compact(
            'ledgers',
            'customers',
            'totalDebit',
            'totalCredit',
            'closingBalance'
        ));
    }

    /**
     * 🔹 Payment Form
     */
    public function createPayment()
    {
        $customers = Customer::all();
        return view('ledger.customer_payment', compact('customers'));
    }

    /**
     * 🔹 Store Payment Entry
     */
    public function storePayment(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:1'
        ]);

        // 🔥 Get last balance
        $lastBalance = CustomerLedger::where('customer_id', $request->customer_id)
            ->latest()
            ->value('balance') ?? 0;

        // 🔥 New balance
        $newBalance = $lastBalance - $request->amount;

        // ✅ Save ledger entry
        CustomerLedger::create([
            'customer_id' => $request->customer_id,
            'reference_id' => null,
            'type' => 'payment',
            'debit' => 0,
            'credit' => $request->amount,
            'balance' => $newBalance,
            'transaction_date' => now(),
            'description' => 'Payment Received',
            'created_by' => Auth::id()
        ]);

        return redirect()
            ->route('customer.ledger')
            ->with('success', 'Payment added successfully');
    }

    /**
     * 🔹 Helper: Get Customer Balance
     */
    public function getBalance($customer_id)
    {
        return CustomerLedger::where('customer_id', $customer_id)
            ->latest()
            ->value('balance') ?? 0;
    }
}