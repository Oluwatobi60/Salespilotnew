<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ManagerCredentials;
use App\Models\Staffs;
use App\Models\UserSubscription;

class AddManagerController extends Controller
{
    public function add_manager()
    {
        $manager = Auth::user();
        $businessName = $manager->business_name;

        // All managers for the business (for main table)
        $managerdata = User::where('business_name', $businessName)
            ->latest()
            ->paginate(4);

        // Only managers where 'addby' is set (not null/empty)
        $delegatedManagers = User::where('business_name', $businessName)
            ->whereNotNull('addby')
            ->where('addby', '!=', '')
            ->latest()
            ->get();

        return view('manager.staff.add_manager', compact('managerdata', 'delegatedManagers'));
    }


    public function createmanager(Request $request)
    {
        $validatedData = $request->validate([
            'firstname' => 'required|string|max:255',
            'othername' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'business_name' => 'required|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'state' => 'required|string',
            'local_govt' => 'required|string',
            'address' => 'nullable|string',
            'status' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if email or phone exists in staffs table
        $staffEmailExists = Staffs::where('email', $validatedData['email'])->exists();
        $staffPhoneExists = !empty($validatedData['phone']) && Staffs::where('phone', $validatedData['phone'])->exists();
        if ($staffEmailExists) {
            return redirect()->back()->withErrors(['email' => 'This email address is already registered as a staff.'])->withInput();
        }
        if ($staffPhoneExists) {
            return redirect()->back()->withErrors(['phone' => 'This phone number is already registered as a staff.'])->withInput();
        }

        $sessionManager = Auth::user();
        // Check subscription expiry directly using UserSubscription
        $subscription = UserSubscription::where('user_id', $sessionManager->id)
            ->where('status', 'active')
            ->orderByDesc('end_date')
            ->first();
        if (!$subscription || ($subscription->end_date < now())) {
            return redirect()->back()->with('error', 'Your subscription has expired. You cannot add a new manager.');
        }

        // Handle file upload for business_logo
        $businessLogoPath = $sessionManager->business_logo;
        if ($request->hasFile('business_logo')) {
            $image = $request->file('business_logo');
            $imageName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('business_logos'), $imageName);
            $businessLogoPath = 'business_logos/'.$imageName;
        }

        $status = strtolower($validatedData['status']) === 'active' ? 1 : 0;

        $user = User::create([
            'first_name' => $validatedData['firstname'],
            'other_name' => $validatedData['othername'],
            'surname' => $validatedData['surname'],
            'email' => $validatedData['email'],
            'phone_number' => $validatedData['phone'],
            'business_name' => $sessionManager->business_name,
            'branch_name' => $validatedData['branch_name'] ?? null,
            'business_logo' => $businessLogoPath,
            'state' => $validatedData['state'],
            'local_govt' => $validatedData['local_govt'],
            'addby' => $sessionManager->email,
            'address' => $validatedData['address'] ?? null,
            'role' => 'manager',
            'status' => $status,
            'password' => Hash::make($validatedData['password']),
        ]);

        // Send login details to email using a manager-specific mailable
        try {
            Mail::to($user->email)->send(new ManagerCredentials(
                $user,
                $validatedData['password'],
                $sessionManager->business_name,
                $user->first_name . ' ' . $user->surname
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Mail error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Manager added successfully! Login details have been sent to their email.');
    }



    public function editmanager($id)
    {
        $manageredit = User::findOrFail($id);
        return view('manager.staff.edit_manager', compact('manageredit'));
    }



    public function updatemanager(Request $request, $id)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'other_name' => 'required|string|max:255|unique:users,other_name,' . $id,
            'phone' => 'nullable|string|max:20',
        ]);

        $manager = User::findOrFail($id);
        $manager->surname = $validatedData['surname'];
        $manager->first_name = $validatedData['first_name'];
        $manager->other_name = $validatedData['other_name'];
        $manager->phone_number = $validatedData['phone'];
        $manager->save();

        return redirect()->route('manager.manager')->with('success', 'Manager profile updated successfully.');
    }



    public function toggleStatus(Request $request, $id)
    {
        // Find the manager by ID
        $manager = User::findOrFail($id);
        // Toggle the status
        $manager->status = !$manager->status;
        $manager->save();

        // Prepare status text for the flash message
        $statusText = $manager->status ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Manager has been {$statusText} successfully.");
    }
}



