<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch\Branch;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class BranchController extends Controller
{
    /**
     * Display a listing of branches
     */
    public function index()
    {
        $user = Auth::user();

        // Get branches for the logged-in user's business
        $branches = Branch::where('user_id', $user->id)
            ->with(['manager', 'subscriptionPlan'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get available managers for assignment (only unassigned managers)
        $assignedManagerIds = Branch::where('user_id', $user->id)
            ->whereNotNull('manager_id')
            ->pluck('manager_id')
            ->toArray();

        $managers = User::where('business_name', $user->business_name)
            ->where('role', 'manager')
            ->where('status', 1)
            ->whereNotIn('id', $assignedManagerIds)
            ->get();

        // Get active subscription
        $activeSubscription = $user->currentSubscription()->first();

        // Check if user is business creator
        $isBusinessCreator = $user->isBusinessCreator();

        ActivityLogger::log('view_branches', 'Viewed branches page');

        return view('manager.branch.branches', compact('branches', 'managers', 'activeSubscription', 'isBusinessCreator'));
    }

    /**
     * Store a newly created branch
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Only business creator can create branches
        if (!$user->isBusinessCreator()) {
            return redirect()->back()->with('error', 'Only the business owner can create branches.');
        }

        // Check subscription and enforce limits
        $subscription = $user->currentSubscription()->first();
        if (!$subscription || !$subscription->subscriptionPlan) {
            return redirect()->back()
                ->with('error', 'No active subscription found. Please subscribe to a plan.')
                ->with('upgrade_url', route('plan_pricing'));
        }

        $plan = $subscription->subscriptionPlan;
        $planName = strtolower($plan->plan_name);
        $currentBranchCount = Branch::where('user_id', $user->id)->count();

        // Free and Basic plans cannot create branches
        if (in_array($planName, ['free', 'basic'])) {
            return redirect()->back()
                ->with('error', 'Branch creation is not available on your current plan.')
                ->with('upgrade_url', route('plan_pricing'));
        }

        // Standard plan: max 2 branches
        if ($planName === 'standard' && $currentBranchCount >= 2) {
            return redirect()->back()
                ->with('error', 'You have reached your branch limit (2 branches). Upgrade to Premium for unlimited branches.')
                ->with('upgrade_url', route('plan_pricing'));
        }

        // Premium plan: unlimited branches (no check needed)

        $validated = $request->validate([
            'branch_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'state' => 'required|string|max:255',
            'local_govt' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Active,Inactive,1,0',
        ]);

        // Check if manager is already assigned to another branch
        if (!empty($validated['manager_id'])) {
            $existingBranch = Branch::where('manager_id', $validated['manager_id'])
                ->where('user_id', $user->id)
                ->first();

            if ($existingBranch) {
                return redirect()->back()
                    ->with('error', 'This manager is already assigned to branch: ' . $existingBranch->branch_name . '. A manager can only be assigned to one branch.')
                    ->withInput();
            }
        }

        // Convert status to integer
        $validated['status'] = in_array($validated['status'], ['Active', '1', 1]) ? 1 : 0;
        $validated['user_id'] = $user->id;
        $validated['business_name'] = $user->business_name;

        // Add subscription information
        if ($subscription) {
            $validated['subscription_plan_id'] = $subscription->subscription_plan_id;
            $validated['user_subscription_id'] = $subscription->id;
        }

        $branch = Branch::create($validated);

        ActivityLogger::log('create_branch', 'Created new branch: ' . $branch->branch_name);

        return redirect()->route('manager.branches')
            ->with('success', 'Branch created successfully!');
    }

    /**
     * Display the specified branch
     */
    public function show($id)
    {
        $user = Auth::user();
        $branch = Branch::where('user_id', $user->id)
            ->with(['manager', 'subscriptionPlan'])
            ->findOrFail($id);

        ActivityLogger::log('view_branch', 'Viewed branch: ' . $branch->branch_name);

        return response()->json($branch);
    }

    /**
     * Show the form for editing the specified branch
     */
    public function edit($id)
    {
        $user = Auth::user();
        $branch = Branch::where('user_id', $user->id)->findOrFail($id);

        // Get assigned manager IDs excluding current branch's manager
        $assignedManagerIds = Branch::where('user_id', $user->id)
            ->whereNotNull('manager_id')
            ->where('id', '!=', $id)
            ->pluck('manager_id')
            ->toArray();

        $managers = User::where('business_name', $user->business_name)
            ->where('role', 'manager')
            ->where('status', 1)
            ->whereNotIn('id', $assignedManagerIds)
            ->get();

        return response()->json([
            'branch' => $branch,
            'managers' => $managers
        ]);
    }

    /**
     * Update the specified branch
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Only business creator can update branches
        if (!$user->isBusinessCreator()) {
            return redirect()->back()->with('error', 'Only the business owner can update branches.');
        }

        $branch = Branch::where('user_id', $user->id)->findOrFail($id);

        $validated = $request->validate([
            'branch_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'state' => 'required|string|max:255',
            'local_govt' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Active,Inactive,1,0',
        ]);

        // Check if manager is already assigned to another branch (excluding current branch)
        if (!empty($validated['manager_id'])) {
            $existingBranch = Branch::where('manager_id', $validated['manager_id'])
                ->where('user_id', $user->id)
                ->where('id', '!=', $id)
                ->first();

            if ($existingBranch) {
                return redirect()->back()
                    ->with('error', 'This manager is already assigned to branch: ' . $existingBranch->branch_name . '. A manager can only be assigned to one branch.')
                    ->withInput();
            }
        }

        // Convert status to integer
        $validated['status'] = in_array($validated['status'], ['Active', '1', 1]) ? 1 : 0;

        $branch->update($validated);

        ActivityLogger::log('update_branch', 'Updated branch: ' . $branch->branch_name);

        return redirect()->route('manager.branches')
            ->with('success', 'Branch updated successfully!');
    }

    /**
     * Toggle branch status
     */
    public function toggleStatus($id)
    {
        $user = Auth::user();

        // Only business creator can toggle branch status
        if (!$user->isBusinessCreator()) {
            return redirect()->back()->with('error', 'Only the business owner can modify branch status.');
        }

        $branch = Branch::where('user_id', $user->id)->findOrFail($id);

        $branch->status = $branch->status == 1 ? 0 : 1;
        $branch->save();

        $statusText = $branch->status == 1 ? 'activated' : 'deactivated';
        ActivityLogger::log('toggle_branch_status', 'Branch ' . $statusText . ': ' . $branch->branch_name);

        return redirect()->back()
            ->with('success', 'Branch status updated successfully!');
    }

    /**
     * Remove the specified branch
     */
    public function destroy($id)
    {
        $user = Auth::user();

        // Only business creator can delete branches
        if (!$user->isBusinessCreator()) {
            return redirect()->back()->with('error', 'Only the business owner can delete branches.');
        }

        $branch = Branch::where('user_id', $user->id)->findOrFail($id);
        $branchName = $branch->branch_name;

        $branch->delete();

        ActivityLogger::log('delete_branch', 'Deleted branch: ' . $branchName);

        return redirect()->route('manager.branches')
            ->with('success', 'Branch deleted successfully!');
    }
}
