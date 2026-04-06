<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{

    public function index()
    {
        try {

            $doctors = Doctor::latest()->paginate(10);

            return view('master.doctors.index', compact('doctors'));

        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }


    public function create()
    {
        try {

            return view('master.doctors.create');

        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }


    public function store(Request $request)
    {

        DB::beginTransaction();

        try {

            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            Doctor::create([

                'name' => $request->name,
                'contact' => $request->contact,
                'email' => $request->email,
                'registration_number' => $request->registration_number,
                'professional_credential' => $request->professional_credential,
                'medical_speciality' => $request->medical_speciality,
                'clinic_name' => $request->clinic_name,
                'clinic_city' => $request->clinic_city,
                'clinic_pincode' => $request->clinic_pincode,
                'clinic_address' => $request->clinic_address

            ]);

            DB::commit();

            return redirect()
                ->route('doctors.index')
                ->with('success','Doctor Added Successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withInput()->with('error',$e->getMessage());
        }

    }


    public function edit(Doctor $doctor)
    {
        try {

            return view('master.doctors.edit', compact('doctor'));

        } catch (\Exception $e) {

            return back()->with('error',$e->getMessage());
        }
    }


    public function update(Request $request, Doctor $doctor)
    {

        DB::beginTransaction();

        try {

            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $doctor->update([

                'name' => $request->name,
                'contact' => $request->contact,
                'email' => $request->email,
                'registration_number' => $request->registration_number,
                'professional_credential' => $request->professional_credential,
                'medical_speciality' => $request->medical_speciality,
                'clinic_name' => $request->clinic_name,
                'clinic_city' => $request->clinic_city,
                'clinic_pincode' => $request->clinic_pincode,
                'clinic_address' => $request->clinic_address

            ]);

            DB::commit();

            return redirect()
                ->route('doctors.index')
                ->with('success','Doctor Updated Successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withInput()->with('error',$e->getMessage());
        }

    }


    public function destroy(Doctor $doctor)
    {

        DB::beginTransaction();

        try {

            $customerExists = Customer::where('doctor_id',$doctor->id)->exists();

            if ($customerExists) {

                return back()->with(
                    'error',
                    'Cannot delete doctor because customers are linked.'
                );

            }

            $doctor->delete();

            DB::commit();

            return redirect()
                ->route('doctors.index')
                ->with('success','Doctor Deleted Successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error',$e->getMessage());
        }

    }

}