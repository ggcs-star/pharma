<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Payment List
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $payments = Payment::with('customer')
            ->latest()
            ->paginate(15);

        return view('payments.index', compact('payments'));
    }

    /*
    |--------------------------------------------------------------------------
    | Create Payment Form
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $customers = Customer::all();
        return view('payments.create', compact('customers'));
    }

    /*
    |--------------------------------------------------------------------------
    | Store Payment
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount'      => 'required|numeric|min:1',
            'payment_date'=> 'nullable|date',
            'payment_mode'=> 'nullable|string|max:50',
        ]);

        DB::beginTransaction();

        try {

            $payment = Payment::create([
                'customer_id' => $request->customer_id,
                'payment_date'=> $request->payment_date ?? now(),
                'amount'      => $request->amount,
                'payment_mode'=> $request->payment_mode,
                'reference_no'=> $request->reference_no,
                'remarks'     => $request->remarks,
                'user_id'     => auth()->id()
            ]);

            /*
            |--------------------------------------------------------------
            | SAFE LEDGER BALANCE FETCH
            |--------------------------------------------------------------
            */
            $currentBalance = LedgerEntry::where('entity_type','customer')
                ->where('entity_id',$request->customer_id)
                ->orderBy('id','desc')
                ->value('balance_after') ?? 0;

            $newBalance = $currentBalance - $request->amount;

            /*
            |--------------------------------------------------------------
            | LEDGER ENTRY (CREDIT)
            |--------------------------------------------------------------
            */
            LedgerEntry::create([
                'entity_type'   => 'customer',
                'entity_id'     => $request->customer_id,
                'debit'         => 0,
                'credit'        => $request->amount,
                'balance_after' => $newBalance,
                'reference_id'  => $payment->id,
                'reference_type'=> 'CustomerPayment',
                'remarks'       => 'Customer payment received',
                'user_id'       => auth()->id()
            ]);

            DB::commit();

            return redirect()
                ->route('payments.index')
                ->with('success','Payment Recorded Successfully');

        } catch(\Exception $e){

            DB::rollBack();
            return back()->withInput()->with('error',$e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Payment
    |--------------------------------------------------------------------------
    */
    public function edit(Payment $payment)
    {
        $customers = Customer::all();
        return view('payments.edit', compact('payment','customers'));
    }

    /*
    |--------------------------------------------------------------------------
    | Update Payment (🔥 SAFE VERSION)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount'      => 'required|numeric|min:1',
            'payment_date'=> 'nullable|date',
            'payment_mode'=> 'nullable|string|max:50',
        ]);

        DB::beginTransaction();

        try {

            $oldCustomerId = $payment->customer_id;
            $oldAmount     = $payment->amount;

            /*
            |--------------------------------------------------------------
            | REVERSE OLD LEDGER (OLD CUSTOMER)
            |--------------------------------------------------------------
            */
            $oldBalance = LedgerEntry::where('entity_type','customer')
                ->where('entity_id',$oldCustomerId)
                ->orderBy('id','desc')
                ->value('balance_after') ?? 0;

            LedgerEntry::create([
                'entity_type'   => 'customer',
                'entity_id'     => $oldCustomerId,
                'debit'         => $oldAmount,
                'credit'        => 0,
                'balance_after' => $oldBalance + $oldAmount,
                'reference_id'  => $payment->id,
                'reference_type'=> 'PaymentUpdateReverse',
                'remarks'       => 'Reversing old payment',
                'user_id'       => auth()->id()
            ]);

            /*
            |--------------------------------------------------------------
            | UPDATE PAYMENT
            |--------------------------------------------------------------
            */
            $payment->update([
                'customer_id' => $request->customer_id,
                'payment_date'=> $request->payment_date,
                'amount'      => $request->amount,
                'payment_mode'=> $request->payment_mode,
                'reference_no'=> $request->reference_no,
                'remarks'     => $request->remarks,
            ]);

            /*
            |--------------------------------------------------------------
            | NEW LEDGER ENTRY (NEW CUSTOMER)
            |--------------------------------------------------------------
            */
            $newBalanceBase = LedgerEntry::where('entity_type','customer')
                ->where('entity_id',$request->customer_id)
                ->orderBy('id','desc')
                ->value('balance_after') ?? 0;

            $newBalance = $newBalanceBase - $request->amount;

            LedgerEntry::create([
                'entity_type'   => 'customer',
                'entity_id'     => $request->customer_id,
                'debit'         => 0,
                'credit'        => $request->amount,
                'balance_after' => $newBalance,
                'reference_id'  => $payment->id,
                'reference_type'=> 'CustomerPaymentUpdated',
                'remarks'       => 'Updated payment',
                'user_id'       => auth()->id()
            ]);

            DB::commit();

            return redirect()
                ->route('payments.index')
                ->with('success','Payment Updated Successfully');

        } catch(\Exception $e){

            DB::rollBack();
            return back()->withInput()->with('error',$e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Payment (🔥 SAFE REVERSE)
    |--------------------------------------------------------------------------
    */
    public function destroy(Payment $payment)
    {
        DB::beginTransaction();

        try {

            if(!$payment){
                return back()->with('error','Payment not found');
            }

            /*
            |--------------------------------------------------------------
            | REVERSE LEDGER
            |--------------------------------------------------------------
            */
            $currentBalance = LedgerEntry::where('entity_type','customer')
                ->where('entity_id',$payment->customer_id)
                ->orderBy('id','desc')
                ->value('balance_after') ?? 0;

            LedgerEntry::create([
                'entity_type'   => 'customer',
                'entity_id'     => $payment->customer_id,
                'debit'         => $payment->amount,
                'credit'        => 0,
                'balance_after' => $currentBalance + $payment->amount,
                'reference_id'  => $payment->id,
                'reference_type'=> 'PaymentDelete',
                'remarks'       => 'Payment deleted',
                'user_id'       => auth()->id()
            ]);

            $payment->delete();

            DB::commit();

            return redirect()
                ->route('payments.index')
                ->with('success','Payment Deleted Successfully');

        } catch(\Exception $e){

            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }
}