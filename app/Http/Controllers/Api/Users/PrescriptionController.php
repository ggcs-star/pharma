<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prescription;
use Illuminate\Support\Facades\Storage;

class PrescriptionController extends Controller
{
    /**
     * Upload Prescription
     */
public function upload(Request $request)
{
    try {

        // ✅ Step 1: Validate
        $validated = $request->validate([
            'prescription' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes' => 'nullable|string|max:500'
        ]);

        // ✅ Step 2: Check file exists
        if (!$request->hasFile('prescription')) {
            dd('File not received');
        }

        $file = $request->file('prescription');

        // ✅ DEBUG: file info
        \Log::info('File received', [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType()
        ]);

        $fileName = time().'_'.$file->getClientOriginalName();

        // ✅ Step 3: Upload to S3
        $path = Storage::disk('s3')->putFileAs(
            'prescriptions',
            $file,
            $fileName
        );

        // ❌ अगर यहाँ crash हो रहा है तो S3 issue है
        if (!$path) {
            dd('S3 upload failed');
        }

        \Log::info('S3 Path', ['path' => $path]);

        // ✅ Step 4: Get URL
        $url = Storage::disk('s3')->url($path);

        if (!$url) {
            dd('URL generation failed');
        }

        \Log::info('File URL', ['url' => $url]);

        // ✅ Step 5: Auth check
        $userId = auth('sanctum')->id();

        if (!$userId) {
            dd('User not authenticated');
        }

        // ✅ Step 6: DB Save
        $prescription = Prescription::create([
    'user_id' => $userId,
    'file_path' => $url,
    'notes' => $request->notes,
    'status' => 'pending' // 🔥 ADD THIS
]);
        \Log::info('DB Saved', ['id' => $prescription->id]);

        return response()->json([
            'status' => true,
            'message' => 'Prescription uploaded successfully',
'data' => [
    'id' => $prescription->id,
    'file' => $prescription->file_path
]        ]);

    } catch (\Exception $e) {

        // ✅ FULL ERROR LOG
        \Log::error('Prescription Upload Error', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString()
        ]);

        // ✅ TEMP DEBUG (remove after fix)
        dd($e->getMessage());

        return response()->json([
            'status' => false,
            'message' => 'Upload failed',
            'error' => $e->getMessage()
        ], 500);
    }
}
    /**
     * Check if user has uploaded prescription
     */
    public function check()
    {
        $exists = Prescription::where('user_id', auth()->id())->exists();

        return response()->json([
            'status' => true,
            'uploaded' => $exists
        ]);
    }

    /**
     * Get all prescriptions of user
     */
    public function myPrescriptions()
    {
        $data = Prescription::where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * Delete prescription
     */
    public function delete($id)
    {
        $prescription = Prescription::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$prescription) {
            return response()->json([
                'status' => false,
                'message' => 'Not found'
            ], 404);
        }

      if ($prescription->file_path) {
    $path = parse_url($prescription->file_path, PHP_URL_PATH);
    $path = ltrim($path, '/');

    Storage::disk('s3')->delete($path);
}
        $prescription->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully'
        ]);
    }
}