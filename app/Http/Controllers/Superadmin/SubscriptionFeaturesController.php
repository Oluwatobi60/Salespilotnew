<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SubscriptionFeaturesController extends Controller
{
    /**
     * Display subscription plans with features management
     */
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('monthly_price')->get();
        $features = SubscriptionFeature::getGroupedFeatures();
        $roles = SubscriptionFeature::getRoles();

        return view('superadmin.subscription-features.index', compact('plans', 'features', 'roles'));
    }

    /**
     * Toggle a single feature for a plan
     */
    public function toggleFeature(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'feature_slug' => 'required|string|exists:subscription_features,slug',
            'enabled' => 'required|boolean',
        ]);

        $currentFeatures = $plan->features ?? [];
        
        if ($validated['enabled']) {
            // Add feature if not already present
            if (!in_array($validated['feature_slug'], $currentFeatures)) {
                $currentFeatures[] = $validated['feature_slug'];
            }
        } else {
            // Remove feature
            $currentFeatures = array_values(array_filter($currentFeatures, fn($f) => $f !== $validated['feature_slug']));
        }

        $plan->setFeatures($currentFeatures);
        
        // Clear cache
        Cache::forget("subscription_plan_{$plan->id}");
        Cache::forget('active_subscription_plans');

        return response()->json([
            'success' => true,
            'message' => 'Feature ' . ($validated['enabled'] ? 'enabled' : 'disabled') . ' successfully',
            'features_count' => count($currentFeatures),
        ]);
    }

    /**
     * Update features for a specific plan
     */
    public function updatePlanFeatures(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'features' => 'nullable|array',
            'features.*' => 'string|exists:subscription_features,slug',
        ]);

        $plan->setFeatures($validated['features'] ?? []);
        
        // Clear cache
        Cache::forget("subscription_plan_{$plan->id}");
        Cache::forget('active_subscription_plans');

        return response()->json([
            'success' => true,
            'message' => "Features updated successfully for {$plan->name} plan!",
            'features_count' => count($validated['features'] ?? []),
        ]);
    }

    /**
     * Create new feature
     */
    public function createFeature(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_features,slug',
            'description' => 'nullable|string',
            'role' => 'required|in:business_creator,manager,staff,branch',
            'category' => 'nullable|string',
        ]);

        $maxSortOrder = SubscriptionFeature::where('role', $validated['role'])->max('sort_order') ?? 0;
        
        $feature = SubscriptionFeature::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'role' => $validated['role'],
            'category' => $validated['category'] ?? 'general',
            'is_active' => true,
            'sort_order' => $maxSortOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feature created successfully!',
            'feature' => $feature,
        ]);
    }

    /**
     * Delete feature
     */
    public function deleteFeature(SubscriptionFeature $feature)
    {
        // Remove from all plans
        $plans = SubscriptionPlan::all();
        foreach ($plans as $plan) {
            if ($plan->hasFeature($feature->slug)) {
                $plan->removeFeature($feature->slug);
            }
        }

        $feature->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feature deleted successfully!',
        ]);
    }

    /**
     * Update plan basic info
     */
    public function updatePlanInfo(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'monthly_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'max_managers' => 'required|integer|min:1',
            'max_staff' => 'nullable|integer|min:1',
            'max_branches' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'trial_days' => 'nullable|integer|min:0',
        ]);

        $plan->update($validated);

        Cache::forget("subscription_plan_{$plan->id}");
        Cache::forget('active_subscription_plans');

        return response()->json([
            'success' => true,
            'message' => 'Plan updated successfully!',
        ]);
    }

    /**
     * Clone plan features to another plan
     */
    public function cloneFeatures(Request $request)
    {
        $validated = $request->validate([
            'source_plan_id' => 'required|exists:subscription_plans,id',
            'target_plan_id' => 'required|exists:subscription_plans,id|different:source_plan_id',
        ]);

        $sourcePlan = SubscriptionPlan::find($validated['source_plan_id']);
        $targetPlan = SubscriptionPlan::find($validated['target_plan_id']);

        $targetPlan->setFeatures($sourcePlan->features ?? []);

        return response()->json([
            'success' => true,
            'message' => "Features cloned from {$sourcePlan->name} to {$targetPlan->name}!",
        ]);
    }
}
