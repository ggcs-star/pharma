<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /* ===============================
        LIST ADDRESSES
    =============================== */
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    /* ===============================
        STORE ADDRESS
    =============================== */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|digits:10',
            'address_line_1' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|digits:6',
        ]);

        // default handling
        if ($request->is_default) {
            Address::where('user_id', Auth::id())
                ->update(['is_default' => false]);
        }

        $address = Address::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'is_default' => $request->is_default ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully',
            'data' => $address
        ]);
    }

    /* ===============================
        UPDATE ADDRESS
    =============================== */
    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', Auth::id())
            ->findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|digits:10',
            'address_line_1' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|digits:6',
        ]);

        if ($request->is_default) {
            Address::where('user_id', Auth::id())
                ->update(['is_default' => false]);
        }

        $address->update($request->only([
            'name',
            'phone',
            'address_line_1',
            'address_line_2',
            'city',
            'state',
            'pincode',
            'is_default'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data' => $address
        ]);
    }

    /* ===============================
        DELETE ADDRESS
    =============================== */
    public function destroy($id)
    {
        $address = Address::where('user_id', Auth::id())
            ->findOrFail($id);

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    /* ===============================
        SET DEFAULT
    =============================== */
    public function setDefault($id)
    {
        Address::where('user_id', Auth::id())
            ->update(['is_default' => false]);

        $address = Address::where('user_id', Auth::id())
            ->findOrFail($id);

        $address->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Default address set successfully'
        ]);
    }

    /* ===============================
        GET DEFAULT ADDRESS
    =============================== */
    public function default()
    {
        $address = Address::where('user_id', Auth::id())
            ->where('is_default', true)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $address
        ]);
    }
}