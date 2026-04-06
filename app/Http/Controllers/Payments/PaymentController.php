<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Supplier;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function create()
    {
        $customers = Customer::all();
        $suppliers = Supplier::all();

        return view('payments.create', compact('customers','suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|in:customer,supplier',
            'amount'       => 'required|numeric|min:1',
            'payment_mode' => 'required'
        ]);

        try {

            $this->paymentService->createPayment($request->all());

            return redirect()
                ->back()
                ->with('success','Payment Recorded Successfully');

        } catch (\Exception $e) {

            return back()->with('error',$e->getMessage());
        }
    }
}