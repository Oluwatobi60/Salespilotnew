<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StaffProfileController extends Controller
{
    public function staff_profile()
    {
        // Get the authenticated staff member
        $staff = Auth::guard('staff')->user();

        // If no staff is authenticated, redirect to login
        if (!$staff) {
            return redirect()->route('staff.login')->with('error', 'Please login to view your profile');
        }

        return view('staff.profile.profile', compact('staff'));
    }

    public function updatePassword(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            // Get the authenticated staff member
            /** @var \App\Models\Staffs $staff */
            $staff = Auth::guard('staff')->user();

            // Check if staff is authenticated
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            // Check if current password is correct
            if (!Hash::check($request->current_password, $staff->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            // Update the password
            $staff->password = Hash::make($request->new_password);
            $staff->save();

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully!'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating password'
            ], 500);
        }
    }
}
