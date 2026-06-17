<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SecurityAuditLog;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Orders Listing
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Order::with([
            'user',
            'address'
        ]);

        // Date Filter
        if ($request->filled('from_date')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->to_date
            );
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        // Prescription Filter
        if ($request->rx === 'yes') {
            $query->whereNotNull(
                'prescription_id'
            );
        }

        if ($request->rx === 'no') {
            $query->whereNull(
                'prescription_id'
            );
        }

        $orders = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.orders.index',
            compact('orders')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Order Details
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $order = Order::with([
            'user',
            'address',
            'prescription',
            'items',
            'items.item',
            'items.batch'
        ])->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Customer Analytics
        |--------------------------------------------------------------------------
        */
        $userStats = [

            'total_orders' => Order::where(
                'user_id',
                $order->user_id
            )->count(),

            'total_spend' => Order::where(
                'user_id',
                $order->user_id
            )->sum('total'),

            'cancelled_orders' => Order::where(
                'user_id',
                $order->user_id
            )
            ->where(
                'status',
                'cancelled'
            )
            ->count(),

            'completed_orders' => Order::where(
                'user_id',
                $order->user_id
            )
            ->where(
                'status',
                'delivered'
            )
            ->count(),

            'pending_orders' => Order::where(
                'user_id',
                $order->user_id
            )
            ->whereIn(
                'status',
                [
                    'pending',
                    'pending_approval',
                    'processing'
                ]
            )
            ->count(),

            'online_orders' => Order::where(
                'user_id',
                $order->user_id
            )
            ->where(
                'payment_mode',
                'razorpay'
            )
            ->count(),

            'cod_orders' => Order::where(
                'user_id',
                $order->user_id
            )
            ->where(
                'payment_mode',
                'cod'
            )
            ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Security Audit Logs
        |--------------------------------------------------------------------------
        */
        $auditLogs = SecurityAuditLog::where(
                'user_id',
                $order->user_id
            )
            ->latest()
            ->take(100)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Latest Device Activity
        |--------------------------------------------------------------------------
        */
        $latestDevice = SecurityAuditLog::where(
                'user_id',
                $order->user_id
            )
            ->whereNotNull(
                'device_id'
            )
            ->latest()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | High Risk Events
        |--------------------------------------------------------------------------
        */
        $highRiskEvents = SecurityAuditLog::where(
                'user_id',
                $order->user_id
            )
            ->where(
                'severity_score',
                '>=',
                7
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Security Summary
        |--------------------------------------------------------------------------
        */
        $securitySummary = [

            'total_events' => SecurityAuditLog::where(
                'user_id',
                $order->user_id
            )->count(),

            'suspicious_events' => SecurityAuditLog::where(
                'user_id',
                $order->user_id
            )
            ->where(
                'is_suspicious',
                true
            )
            ->count(),

            'blocked_events' => SecurityAuditLog::where(
                'user_id',
                $order->user_id
            )
            ->where(
                'is_blocked',
                true
            )
            ->count(),

            'review_required' => SecurityAuditLog::where(
                'user_id',
                $order->user_id
            )
            ->where(
                'requires_admin_review',
                true
            )
            ->count(),
        ];

        return view(
            'admin.orders.show',
            compact(
                'order',
                'userStats',
                'auditLogs',
                'latestDevice',
                'highRiskEvents',
                'securitySummary'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Order Status
    |--------------------------------------------------------------------------
    */
    public function updateStatus(
        Request $request,
        $id
    ) {
        $request->validate([
            'status' => 'required'
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'status' => $request->status
        ]);

        return back()->with(
            'success',
            'Order status updated successfully'
        );
    }
}

