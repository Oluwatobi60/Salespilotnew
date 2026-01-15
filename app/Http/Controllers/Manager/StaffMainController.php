<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Staffs;

class StaffMainController extends Controller
{

    public function createstaff(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'staff_id' => 'required|string|unique:staffs,staffsid',
                'fullname' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:staffs,username',
                'email' => 'required|string|email|max:255|unique:staffs,email',
                'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15|unique:staffs,phone',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|string',
                'status' => 'nullable|string',
                'address' => 'nullable|string',
                'passport_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'username.unique' => 'This username is already taken. Please choose a different one.',
                'email.unique' => 'This email address is already registered. Please use a different email.',
                'phone.unique' => 'This phone number is already registered. Please use a different phone number.',
                'phone.regex' => 'Please enter a valid phone number with only numbers, spaces, dashes, or parentheses.',
                'phone.min' => 'Phone number must be at least 10 characters.',
                'password.min' => 'Password must be at least 8 characters long.',
                'password.confirmed' => 'Password confirmation does not match.',
            ]);

            // Hash the password
            $validatedData['password'] = Hash::make($validatedData['password']);

            // Map staff_id to staffsid for database
            $validatedData['staffsid'] = $validatedData['staff_id'];
            unset($validatedData['staff_id']);

            // Handle file upload
            if($request->hasFile('passport_photo')) {
                $image = $request->file('passport_photo');
                $imageName = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('uploads/staff_photos'), $imageName);
                $validatedData['passport_photo'] = 'uploads/staff_photos/'.$imageName;
            }

            // Create a new staff member
            Staffs::create($validatedData);

            // Check if it's an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Staff member added successfully!'
                ]);
            }

            // Redirect back with a success message
            return redirect()->route('manager.staff')->with('success', 'Staff member added successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function add_staff()
    {
        $staffdata = Staffs::latest()->paginate(4);
        return view('manager.staff.add_staff', compact('staffdata'));
    }

    public function editstaff($id)
    {
        $staffedit = Staffs::findOrFail($id);
        return view('manager.staff.edit', compact('staffedit'));
    }


    public function updatestaff(Request $request, $id)
    {
        try {
            // Find the staff member
            $staff = Staffs::findOrFail($id);

            // Validate the incoming request data
            $validatedData = $request->validate([
                'staff_id' => 'required|string|unique:staffs,staffsid,'.$staff->id,
                'fullname' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:staffs,username,'.$staff->id,
                'email' => 'required|string|email|max:255|unique:staffs,email,'.$staff->id,
                'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15|unique:staffs,phone,'.$staff->id,
                'password' => 'nullable|string|min:8|confirmed',
                'role' => 'required|string',
                'status' => 'nullable|string',
                'address' => 'nullable|string',
                'passport_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'username.unique' => 'This username is already taken. Please choose a different one.',
                'email.unique' => 'This email address is already registered. Please use a different email.',
                'phone.unique' => 'This phone number is already registered. Please use a different phone number.',
                'phone.regex' => 'Please enter a valid phone number with only numbers, spaces, dashes, or parentheses.',
                'phone.min' => 'Phone number must be at least 10 characters.',
                'password.min' => 'Password must be at least 8 characters long.',
                'password.confirmed' => 'Password confirmation does not match.',
            ]);

            // Only update password if provided
            if ($request->filled('password')) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            } else {
                unset($validatedData['password']);
            }

            // Map staff_id to staffsid for database
            if (isset($validatedData['staff_id'])) {
                $validatedData['staffsid'] = $validatedData['staff_id'];
                unset($validatedData['staff_id']);
            }

            // Handle file upload
            if($request->hasFile('passport_photo')) {
                // Delete old photo if exists
                if($staff->passport_photo && file_exists(public_path($staff->passport_photo))) {
                    unlink(public_path($staff->passport_photo));
                }

                $image = $request->file('passport_photo');
                $imageName = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('uploads/staff_photos'), $imageName);
                $validatedData['passport_photo'] = 'uploads/staff_photos/'.$imageName;
            }

            // Update the staff member
            $staff->update($validatedData);

            // Redirect back with a success message
            return redirect()->route('manager.staff')->with('success', 'Staff member updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->route('manager.staff')->with('success', 'Staff member updated successfully.');
        }
    }


    public function deletestaff($id)
    {
        try {
            // Find the staff member
            $staff = Staffs::findOrFail($id);

            // Delete passport photo if exists
            if($staff->passport_photo && file_exists(public_path($staff->passport_photo))) {
                unlink(public_path($staff->passport_photo));
            }

            // Delete the staff member
            $staff->delete();

            // Redirect back with a success message
            return redirect()->route('manager.staff')->with('success', 'Staff member deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->route('manager.staff')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
