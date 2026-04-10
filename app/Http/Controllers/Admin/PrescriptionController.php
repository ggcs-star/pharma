<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    // 📌 List Page
    public function index()
    {
        $data = Prescription::with('user')->latest()->get();

        return view('admin.prescriptions.index', compact('data'));
    }

    // 📌 Status Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $prescription = Prescription::findOrFail($id);

        $prescription->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Status updated');
    }
}