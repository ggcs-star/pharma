<?php


namespace App\Http\Controllers\Supplier\Auth;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class SupplierAuthController extends Controller
{
//     public function register(Request $request)
// {
//     $request->validate([
//         'name' => 'required|string|max:255',
//         'phone' => 'required|unique:suppliers',
//         'email' => 'required|email|unique:suppliers',
//         'password' => 'required|min:6',
//     ]);

//     $supplier = Supplier::create([
//         'name' => $request->name,
//         'phone' => $request->phone,
//         'email' => $request->email,
//         'password' => Hash::make($request->password),
//     ]);

//     return response()->json([
//         'message' => 'Supplier registered successfully',
//         'data' => $supplier
//     ]);
// }




public function showLogin()
{
    return view('supplier.auth.login');
}

public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::guard('supplier')->attempt($credentials)) {
        return redirect()->route('supplier.dashboard');
    }

    return back()->with('error', 'Invalid credentials');
}

public function logout()
{
    Auth::guard('supplier')->logout();
    return redirect()->route('supplier.login');
}
}