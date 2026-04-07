<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class PlansController extends Controller
{
    public function index(Request $request)
    {
        $plans = SubscriptionPlan::withCount('userSubscriptions')
            ->orderBy('monthly_price')
            ->get();

        return view('superadmin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('superadmin.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100|unique:subscription_plans,name',
            'monthly_price' => 'required|numeric|min:0',
            'description'   => 'nullable|string|max:500',
            'features'      => 'nullable|string',
            'max_managers'  => 'required|integer|min:1',
            'max_staff'     => 'nullable|integer|min:1',
            'max_branches'  => 'nullable|integer|min:1',
            'trial_days'    => 'required|integer|min:0',
            'is_popular'    => 'boolean',
            'is_active'     => 'boolean',
        ]);

        $validated['features']   = $this->parseFeatures($request->input('features'));
        $validated['is_active']  = $request->has('is_active');
        $validated['is_popular'] = $request->has('is_popular');

        SubscriptionPlan::create($validated);

        return redirect()->route('superadmin.plans')->with('success', 'Plan created successfully.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('superadmin.plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100|unique:subscription_plans,name,' . $plan->id,
            'monthly_price' => 'required|numeric|min:0',
            'description'   => 'nullable|string|max:500',
            'features'      => 'nullable|string',
            'max_managers'  => 'required|integer|min:1',
            'max_staff'     => 'nullable|integer|min:1',
            'max_branches'  => 'nullable|integer|min:1',
            'trial_days'    => 'required|integer|min:0',
            'is_popular'    => 'boolean',
            'is_active'     => 'boolean',
        ]);

        $validated['features']   = $this->parseFeatures($request->input('features'));
        $validated['is_active']  = $request->has('is_active');
        $validated['is_popular'] = $request->has('is_popular');

        $plan->update($validated);

        return redirect()->route('superadmin.plans')->with('success', 'Plan updated successfully.');
    }

    public function toggleStatus(SubscriptionPlan $plan)
    {
        $plan->is_active = !$plan->is_active;
        $plan->save();
        $label = $plan->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Plan \"{$plan->name}\" has been {$label}.");
    }

    /**
     * Convert newline-separated feature text into a JSON array.
     */
    private function parseFeatures(?string $raw): array
    {
        if (empty($raw)) {
            return [];
        }

        return array_values(
            array_filter(
                array_map('trim', explode("\n", $raw))
            )
        );
    }
}
