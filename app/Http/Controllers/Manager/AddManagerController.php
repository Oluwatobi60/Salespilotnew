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
use App\Models\Branch\Branch;

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
            ->with('managedBranch')
            ->latest()
            ->get();

        // Get active subscription with plan details
        $activeSubscription = $manager->currentSubscription()->with('subscriptionPlan')->first();

        // Get branch count
        $branchCount = Branch::where('user_id', $manager->id)->count();

        // Check if user is business creator
        $isBusinessCreator = $manager->isBusinessCreator();

        return view('manager.staff.add_manager', compact('managerdata', 'delegatedManagers', 'activeSubscription', 'branchCount', 'isBusinessCreator'));
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

        // Check subscription and enforce limits
        $subscription = UserSubscription::where('user_id', $sessionManager->id)
            ->where('status', 'active')
            ->with('subscriptionPlan')
            ->orderByDesc('end_date')
            ->first();

        if (!$subscription || ($subscription->end_date < now())) {
            return redirect()->back()->with('error', 'Your subscription has expired. You cannot add a new manager.');
        }

        // Check manager creation limits based on plan
        $plan = $subscription->subscriptionPlan;
        if (!$plan || empty($plan->name)) {
            // If no plan found, treat as free plan (most restrictive)
            return redirect()->back()
                ->with('error', 'Unable to verify your subscription plan. Please contact support or subscribe to a valid plan.');
        }

        $planName = strtolower(trim($plan->name));

        // Count existing managers created by this business (delegated managers)
        $currentManagerCount = User::where('business_name', $sessionManager->business_name)
            ->whereNotNull('addby')
            ->where('addby', '!=', '')
            ->count();

        // Debug: Log the values
        \Log::info('Manager Creation Check', [
            'plan_name' => $planName,
            'current_count' => $currentManagerCount,
            'business_name' => $sessionManager->business_name,
            'plan_id' => $subscription->subscription_plan_id ?? 'null'
        ]);

        // Free plan: max 1 manager
        if ($planName === 'free' && $currentManagerCount >= 1) {
            return redirect()->back()
                ->with('error', 'You have reached your manager limit (1 manager). Upgrade your plan for more managers.');
        }

        // Basic plan: max 1 manager
        if ($planName === 'basic' && $currentManagerCount >= 1) {
            return redirect()->back()
                ->with('error', 'You have reached your manager limit (1 manager). Upgrade to Standard or Premium for more managers.');
        }

        // Standard plan: max 2 managers
        if ($planName === 'standard' && $currentManagerCount >= 2) {
            return redirect()->back()
                ->with('error', 'You have reached your manager limit (2 managers). Upgrade to Premium for more managers.');
        }

        // Premium plan: max 3 managers
        if ($planName === 'premium' && $currentManagerCount >= 3) {
            return redirect()->back()
                ->with('error', 'You have reached your manager limit (3 managers).');
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



