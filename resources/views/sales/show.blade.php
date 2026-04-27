@extends('layouts.master')

@section('content')

<div class="container mt-4">
    <div class="card shadow-xl border-0 rounded-4 overflow-hidden">
        
        {{-- Header with Pharmacy Style --}}
        <div class="text-white px-4 py-3" style="background: linear-gradient(135deg, #0b2b26 0%, #163832 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 fw-bold">
                        <i class="ri-hospital-line me-2"></i> PharmaSphere 360
                    </h3>
                    <small class="opacity-75">Your Trusted Healthcare Partner</small>
                </div>
                <div class="text-end">
                    <div class="small opacity-75">INVOICE</div>
                    <div class="fw-bold fs-5">#{{ $sale->bill_number }}</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            
            {{-- Invoice Meta Row --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 bg-light">
                        <div class="text-muted small mb-2">
                            <i class="ri-calendar-check-line me-1"></i> INVOICE DETAILS
                        </div>
                        <div class="row">
                            <div class="col-5 text-muted">Invoice No:</div>
                            <div class="col-7 fw-semibold">{{ $sale->bill_number }}</div>
                            
                            <div class="col-5 text-muted">Invoice Date:</div>
                            <div class="col-7 fw-semibold">{{ \Carbon\Carbon::parse($sale->bill_date)->format('d/m/Y') }}</div>
                            
                            <div class="col-5 text-muted">Order Type:</div>
                            <div class="col-7"><span class="badge bg-success">Retail Sale</span></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mt-3 mt-md-0">
                    <div class="border rounded-3 p-3 bg-light">
                        <div class="text-muted small mb-2">
                            <i class="ri-store-line me-1"></i> STORE DETAILS
                        </div>
                        <div class="fw-semibold">PharmaSphere 360 Medical Store</div>
                        <div class="small text-muted">123, Healthcare Avenue, Near City Hospital</div>
                        <div class="small text-muted">Mumbai - 400001 | Ph: +91 98765 43210</div>
                        <div class="small text-muted">GST: 27ABCDE1234F1Z | DL: MH-1234</div>
                    </div>
                </div>
            </div>

            {{-- Customer & Doctor Section --}}
            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <div class="border rounded-3 p-3" style="background: #f8f9fc;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-primary bg-opacity-10 p-2 rounded me-2">
                                <i class="ri-user-line text-primary"></i>
                            </div>
                            <span class="fw-semibold text-primary">CUSTOMER DETAILS</span>
                        </div>
                        <div class="ps-4">
                            <div class="fw-bold fs-5">{{ $sale->customer->name ?? 'Walk-in Customer' }}</div>
                            @if($sale->customer)
                                <div class="small text-muted">
                                    @if($sale->customer->mobile)
                                        <i class="ri-phone-line me-1"></i> {{ $sale->customer->mobile }}<br>
                                    @endif
                                    @if($sale->customer->address)
                                        <i class="ri-map-pin-line me-1"></i> {{ $sale->customer->address }}
                                    @endif
                                </div>
                            @else
                                <div class="small text-muted"><i class="ri-information-line me-1"></i> Walk-in Customer (No registration)</div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="border rounded-3 p-3" style="background: #f8f9fc;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-success bg-opacity-10 p-2 rounded me-2">
                                <i class="ri-stethoscope-line text-success"></i>
                            </div>
                            <span class="fw-semibold text-success">DOCTOR / REFERRAL</span>
                        </div>
                        <div class="ps-4">
                            @if(isset($sale->doctor) && $sale->doctor)
                                <div class="fw-bold">{{ $sale->doctor->name ?? $sale->doctor_name ?? 'N/A' }}</div>
                                <div class="small text-muted">
                                    @if(isset($sale->doctor->qualification))
                                        <i class="ri-graduation-cap-line me-1"></i> {{ $sale->doctor->qualification }}<br>
                                    @endif
                                    @if(isset($sale->doctor->registration_no))
                                        <i class="ri-id-card-line me-1"></i> Reg No: {{ $sale->doctor->registration_no }}
                                    @endif
                                </div>
                            @else
                                <div class="text-muted small">
                                    <i class="ri-information-line me-1"></i> Self / No Referral
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items Table with better styling --}}
            <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="ri-medicine-bottle-line text-primary me-2"></i>
                    <span class="fw-semibold">PRESCRIPTION DETAILS</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-dark">
                            <tr class="text-center">
                                <th style="width: 5%">#</th>
                                <th style="width: 45%">MEDICINE NAME</th>
                                <th style="width: 15%">QTY</th>
                                <th style="width: 17%">MRP (₹)</th>
                                <th style="width: 18%">AMOUNT (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $item->item->name ?? $item->medicine_name ?? 'N/A' }}</span>
                                    @if($item->item && $item->item->strength)
                                        <small class="text-muted d-block">{{ $item->item->strength }}</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
<td class="text-end">
    ₹ {{ number_format($item->selling_price, 2) }}
</td>                                <td class="text-end fw-semibold">₹ {{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-semibold">Subtotal:</td>
                                <td class="text-end fw-semibold">₹ {{ number_format($sale->net_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">GST (5%):</td>
                                <td class="text-end">₹ {{ number_format($sale->net_amount * 0.05, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">Discount:</td>
                                <td class="text-end">₹ {{ number_format($sale->discount ?? 0, 2) }}</td>
                            </tr>
                            <tr class="table-primary">
                                <td colspan="4" class="text-end fw-bold fs-5">TOTAL AMOUNT:</td>
                                <td class="text-end fw-bold fs-5 text-success">₹ {{ number_format($sale->net_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Amount in Words --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="bg-light p-3 rounded-3">
                        <span class="text-muted small">Amount in Words:</span>
                        <span class="fw-semibold ms-2">{{ ucwords(convertNumberToWords($sale->net_amount)) }} Rupees Only</span>
                    </div>
                </div>
            </div>

            {{-- Terms & Signature Section --}}
            <div class="row mt-4 pt-2 border-top">
                <div class="col-md-8">
                    <div class="small text-muted">
                        <strong>Terms & Conditions:</strong>
                        <ul class="ps-3 mb-0 small">
                            <li>Items sold are not returnable or exchangeable</li>
                            <li>Prescription is mandatory for scheduled drugs</li>
                            <li>Please verify the medicines at the time of delivery</li>
                            <li>This is a system generated invoice - no signature required</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 text-center text-md-end mt-3 mt-md-0">
                    <div class="border-top pt-2 d-inline-block" style="min-width: 200px;">
                        <div class="small text-muted">For PharmaSphere 360</div>
                        <div class="mt-3 mb-1">
                            <i class="ri-checkbox-circle-line text-success fs-4"></i>
                        </div>
                        <div class="small fw-semibold">Authorized Signatory</div>
                    </div>
                </div>
            </div>

            {{-- Thank You Note --}}
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <div class="alert alert-success bg-opacity-10 border-0 rounded-3 py-2">
                        <i class="ri-heart-3-line text-danger me-1"></i> 
                        Thank you for choosing PharmaSphere 360! Stay Healthy, Stay Safe
                        <i class="ri-heart-3-line text-danger ms-1"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="card-footer bg-white border-0 p-3 d-flex justify-content-between gap-2">
            <button onclick="window.print()" class="btn btn-dark px-4">
                <i class="ri-printer-line me-2"></i> Print Invoice
            </button>
            <button onclick="window.history.back()" class="btn btn-outline-secondary px-4">
                <i class="ri-arrow-left-line me-2"></i> Back
            </button>
        </div>
    </div>
</div>

{{-- Helper function for number to words --}}
@php
function convertNumberToWords($number) {
    $words = [
        '0' => 'Zero', '1' => 'One', '2' => 'Two', '3' => 'Three', '4' => 'Four',
        '5' => 'Five', '6' => 'Six', '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
        '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve', '13' => 'Thirteen',
        '14' => 'Fourteen', '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
        '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty', '30' => 'Thirty',
        '40' => 'Forty', '50' => 'Fifty', '60' => 'Sixty', '70' => 'Seventy',
        '80' => 'Eighty', '90' => 'Ninety'
    ];
    
    if ($number == 0) return 'Zero';
    
    $num = (int) $number;
    $parts = [];
    
    if ($num >= 10000000) {
        $parts[] = $words[floor($num / 10000000)] . ' Crore';
        $num %= 10000000;
    }
    if ($num >= 100000) {
        $parts[] = $words[floor($num / 100000)] . ' Lakh';
        $num %= 100000;
    }
    if ($num >= 1000) {
        $parts[] = $words[floor($num / 1000)] . ' Thousand';
        $num %= 1000;
    }
    if ($num >= 100) {
        $parts[] = $words[floor($num / 100)] . ' Hundred';
        $num %= 100;
    }
    if ($num > 0) {
        if ($num <= 20) {
            $parts[] = $words[$num];
        } else {
            $tens = floor($num / 10) * 10;
            $ones = $num % 10;
            $parts[] = $words[$tens] . ($ones ? ' ' . $words[$ones] : '');
        }
    }
    
    return implode(' ', $parts);
}
@endphp

{{-- Print Optimization --}}
<style media="print">
    @page {
        size: A4;
        margin: 8mm;
    }
    body {
        background: white !important;
        padding: 0;
        margin: 0;
    }
    .container {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
    .btn, .card-footer {
        display: none !important;
    }
    .badge, .alert {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .table-dark {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .bg-dark, .bg-primary, .bg-success, .bg-light {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
</style>

@endsection