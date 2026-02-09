<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Staffs;
use App\Mail\StaffCredentials;
use App\Models\UserSubscription;
use App\Models\Branch\Branch;

class StaffMainController extends Controller
{

    public function createstaff(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'staff_id' => 'required|string|unique:staffs,staffsid',
                'fullname' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:staffs,email',
                'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15|unique:staffs,phone',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|string',
                'status' => 'nullable|string',
                'address' => 'nullable|string',
                'passport_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'branch_id' => 'nullable|exists:branches,id',
            ], [
                'email.unique' => 'This email address is already registered. Please use a different email.',
                'phone.unique' => 'This phone number is already registered. Please use a different phone number.',
                'phone.regex' => 'Please enter a valid phone number with only numbers, spaces, dashes, or parentheses.',
                'phone.min' => 'Phone number must be at least 10 characters.',
                'password.min' => 'Password must be at least 8 characters long.',
                'password.confirmed' => 'Password confirmation does not match.',
            ]);

            // Store plain password before hashing (for email)
            $plainPassword = $validatedData['password'];

            // Hash the password
            $validatedData['password'] = Hash::make($validatedData['password']);

            // Map staff_id to staffsid for database
            $validatedData['staffsid'] = $validatedData['staff_id'];
            unset($validatedData['staff_id']);

            // Auto-populate business_name, manager_name, and manager_email from the logged-in manager's user record
            $manager = Auth::user();
            $validatedData['business_name'] = $manager->business_name ?? null;
            $managerFullName = trim(($manager->firstname ?? '') . ' ' . ($manager->othername ?? '') . ' ' . ($manager->surname ?? ''));
            $validatedData['manager_name'] = $managerFullName ?: null;
            $validatedData['manager_email'] = $manager->email ?? null;

            // Check subscription and enforce staff limits
            $subscription = UserSubscription::where('user_id', $manager->id)
                ->where('status', 'active')
                ->orderByDesc('end_date')
                ->first();

            if ($subscription && $subscription->subscriptionPlan) {
                $planName = strtolower($subscription->subscriptionPlan->name);
                $currentStaffCount = Staffs::where('business_name', $manager->business_name)->count();

                // Free plan: max 1 staff
                if ($planName === 'free' && $currentStaffCount >= 1) {
                    return redirect()->back()
                        ->with('error', 'You have reached your staff limit (1 staff member). Upgrade your plan for more staff.');
                }

                // Basic plan: max 2 staff
                if ($planName === 'basic' && $currentStaffCount >= 2) {
                    return redirect()->back()
                        ->with('error', 'You have reached your staff limit (2 staff members). Upgrade to Standard or Premium for more staff.');
                }

                // Standard plan: max 4 staff
                if ($planName === 'standard' && $currentStaffCount >= 4) {
                    return redirect()->back()
                        ->with('error', 'You have reached your staff limit (4 staff members). Upgrade to Premium for unlimited staff.');
                }

                // Premium plan: unlimited staff (no check needed)
            }

            // Handle file upload
            if($request->hasFile('passport_photo')) {
                $image = $request->file('passport_photo');
                $imageName = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('uploads/staff_photos'), $imageName);
                $validatedData['passport_photo'] = 'uploads/staff_photos/'.$imageName;
            }

            // Create a new staff member
            $staff = Staffs::create($validatedData);

            // If branch_id is provided, automatically create a branch record with staff assignment
            if ($request->filled('branch_id')) {
                $selectedBranch = Branch::find($request->input('branch_id'));

                if ($selectedBranch) {
                    Branch::create([
                        'user_id' => $manager->id,
                        'staff_id' => $staff->id,
                        'business_name' => $manager->business_name,
                        'branch_name' => $selectedBranch->branch_name,
                        'address' => $selectedBranch->address,
                        'state' => $selectedBranch->state,
                        'local_govt' => $selectedBranch->local_govt,
                        'manager_id' => $selectedBranch->manager_id,
                        'subscription_plan_id' => $subscription ? $subscription->subscription_plan_id : null,
                        'user_subscription_id' => $subscription ? $subscription->id : null,
                        'status' => 1,
                    ]);
                }
            }

            // Send email with login credentials
            $emailSent = false;
            $emailMessage = '';
            try {
                Mail::to($staff->email)->send(new StaffCredentials(
                    $staff,
                    $plainPassword,
                    $validatedData['business_name'],
                    $validatedData['manager_name']
                ));
                $emailSent = true;
                $emailMessage = 'Login credentials have been sent to the staff member\'s email.';
            } catch (\Exception $e) {
                $emailMessage = 'Staff created but email could not be sent: ' . $e->getMessage();
            }

            // Check if it's an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Staff member added successfully!',
                    'email_sent' => $emailSent,
                    'email_message' => $emailMessage
                ]);
            }

            // Redirect back with a success message
            $successMessage = 'Staff member added successfully.';
            if ($emailSent) {
                $successMessage .= ' Login credentials have been sent to their email.';
            }
            return redirect()->route('manager.staff')->with('success', $successMessage);

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
        $manager = Auth::user();
        $businessName = $manager->business_name;

        $staffdata = Staffs::where('business_name', $businessName)
            ->latest()
            ->paginate(4);

        // Get branches for the dropdown
        $branches = Branch::where('business_name', $businessName)
            ->where('status', 1)
            ->select('id', 'branch_name', 'manager_id')
            ->get();

        return view('manager.staff.add_staff', compact('staffdata', 'branches'));
    }

    public function editstaff($id)
    {
        $manager = Auth::user();
        $businessName = $manager->business_name;

        $staffedit = Staffs::where('business_name', $businessName)
            ->findOrFail($id);
        return view('manager.staff.edit', compact('staffedit'));
    }


    public function updatestaff(Request $request, $id)
    {
        try {
            // Find the staff member - ensure they belong to manager's business
            $manager = Auth::user();
            $businessName = $manager->business_name;
            $staff = Staffs::where('business_name', $businessName)->findOrFail($id);

            // Validate the incoming request data
            $validatedData = $request->validate([
                'staff_id' => 'required|string|unique:staffs,staffsid,'.$staff->id,
                'fullname' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:staffs,email,'.$staff->id,
                'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15|unique:staffs,phone,'.$staff->id,
                'password' => 'nullable|string|min:8|confirmed',
                'role' => 'required|string',
                'status' => 'nullable|string',
                'address' => 'nullable|string',
                'passport_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
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

            // Auto-populate business_name, manager_name, and manager_email from the logged-in manager's user record
            $manager = Auth::user();
            $validatedData['business_name'] = $manager->business_name ?? null;
            $managerFullName = trim(($manager->firstname ?? '') . ' ' . ($manager->othername ?? '') . ' ' . ($manager->surname ?? ''));
            $validatedData['manager_name'] = $managerFullName ?: null;
            $validatedData['manager_email'] = $manager->email ?? null;

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
            // Find the staff member - ensure they belong to manager's business
            $manager = Auth::user();
            $businessName = $manager->business_name;
            $staff = Staffs::where('business_name', $businessName)->findOrFail($id);

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



    public function toggleStatus(Request $request, $id)
    {
        // Find the manager by ID
        $staff = Staffs::findOrFail($id);
        // Toggle the status
        $staff->status = !$staff->status;
        $staff->save();

        // Prepare status text for the flash message
        $statusText = $staff->status ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Staff has been {$statusText} successfully.");
    }
}
