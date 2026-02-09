<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        /** @var \App\Models\User $manager */
        $manager = Auth::user();
        $manager->load('managedBranch');
        $subscription = $manager->currentSubscription;
        $plan = $subscription ? $subscription->subscriptionPlan : null;
        return view('manager.profile.show', compact('manager', 'subscription', 'plan'));
    }

    public function edit()
    {
        $manager = Auth::user();
        return view('manager.profile.edit', compact('manager'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $manager */
        $manager = Auth::user();
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'othername' => 'nullable|string|max:255',
            'surname' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // Map validated fields to User model fields
        $manager->first_name = $validated['firstname'];
        $manager->other_name = $validated['othername'] ?? null;
        $manager->surname = $validated['surname'];
        $manager->phone_number = $validated['phone'] ?? null;
        $manager->address = $validated['address'] ?? null;
        $manager->save();
        return redirect()->route('manager.profile.show')->with('success', 'Profile updated successfully!');
    }

    public function changePasswordForm()
    {
        return view('manager.profile.change_password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        /** @var \App\Models\User $manager */
        $manager = Auth::user();
        if (!Hash::check($request->current_password, $manager->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }
        $manager->password = bcrypt($request->new_password);
        $manager->save();
        return redirect()->route('manager.profile.show')->with('success', 'Password changed successfully!');
    }
}
