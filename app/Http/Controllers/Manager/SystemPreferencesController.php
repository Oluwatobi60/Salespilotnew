<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch\Branch;
use App\Models\Staffs;
use App\Models\User;

class SystemPreferencesController extends Controller
{
    public function index()
    {
        $manager = Auth::user();
        $currentSubscription = $manager->currentSubscription()->with('subscriptionPlan')->first();
        $isBusinessCreator = $manager->isBusinessCreator();

        // Load branches based on role
        if ($isBusinessCreator) {
            // Business creator sees all branches
            $branches = Branch::where('user_id', $manager->id)
                ->with(['manager', 'staff', 'staffMembers'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Get all staff for business creator
            $staffs = Staffs::where('business_name', $manager->business_name)
                ->with('branches')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Branch manager sees only their assigned branch
            $branches = Branch::where('user_id', $manager->id)
                ->where('branch_name', $manager->branch_name)
                ->with(['manager', 'staff', 'staffMembers'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Get staff assigned to manager's branch
            $branch = Branch::where('user_id', $manager->id)
                ->where('branch_name', $manager->branch_name)
                ->first();
            
            if ($branch) {
                $staffs = $branch->staffMembers;
            } else {
                $staffs = collect();
            }
        }

        return view('manager.settings.system_preferences', compact('manager', 'currentSubscription', 'branches', 'staffs', 'isBusinessCreator'));
    }

  /*   public function update(Request $request)
    {
        $manager = Auth::user();

        $validatedData = $request->validate([
            'business_name' => 'required|string|max:255',
            'currency' => 'required|string|max:10',
            'timezone' => 'required|string|max:255',
        ]);

        $manager->update($validatedData);

        return redirect()->route('manager.system.preferences')->with('success', 'System preferences updated successfully.');
    } */
}
