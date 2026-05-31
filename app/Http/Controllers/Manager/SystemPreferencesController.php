<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch\Branch;
use App\Models\Staffs;
use App\Models\User;
use App\Models\ReceiptSetting;

class SystemPreferencesController extends Controller
{
    public function index()
    {
        $manager = Auth::user();
        $currentSubscription = $manager->currentSubscription()->with('subscriptionPlan')->first();
        $isBusinessCreator = $manager->isBusinessCreator();

        // Get the business owner (creator) information for display
        if ($isBusinessCreator) {
            $businessOwner = $manager;
        } else {
            // For branch managers, fetch the business owner
            $businessOwner = User::where('business_name', $manager->business_name)
                                ->where('addby', null)
                                ->first() ?? $manager;
        }

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

        // Get receipt settings for the business
        $receiptSettings = ReceiptSetting::getForBusiness($manager->business_name);

        return view('manager.settings.system_preferences', compact('manager', 'businessOwner', 'currentSubscription', 'branches', 'staffs', 'isBusinessCreator', 'receiptSettings'));
    }

    public function update(Request $request)
    {
        $manager = Auth::user();

        // Only business creator can update business information
        if (!$manager->isBusinessCreator()) {
            return redirect()->back()->with('error', 'Only business owner can update business information.');
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'business_cac' => 'nullable|string|max:255',
            'business_tin' => 'nullable|string|max:255',
            'business_logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('business_logo')) {
            $logo = $request->file('business_logo');
            $logoName = time() . '_' . $manager->id . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('business_logos'), $logoName);

            // Delete old logo if exists
            if ($manager->business_logo && file_exists(public_path('business_logos/' . $manager->business_logo))) {
                unlink(public_path('business_logos/' . $manager->business_logo));
            }

            $validated['business_logo'] = $logoName;
        }

        $manager->update($validated);

        return redirect()->back()->with('success', 'Business information updated successfully.');
    }

    public function updateReceiptSettings(Request $request)
    {
        $manager = Auth::user();

        // Only business creator can update receipt settings
        if (!$manager->isBusinessCreator()) {
            return redirect()->back()->with('error', 'Only business owner can update receipt settings.');
        }

        $validated = $request->validate([
            'receipt_title' => 'required|string|max:255',
            'header_text' => 'nullable|string|max:500',
            'footer_text' => 'nullable|string|max:500',
            'paper_size' => 'required|string',
            'font_size' => 'required|string',
            'show_invoice_number' => 'nullable|boolean',
            'show_date' => 'nullable|boolean',
            'show_cashier' => 'nullable|boolean',
            'show_logo' => 'nullable|boolean',
            'show_barcode' => 'nullable|boolean',
            'show_tax_details' => 'nullable|boolean',
            'show_item_codes' => 'nullable|boolean',
            'show_discounts' => 'nullable|boolean',
        ]);

        // Convert checkboxes to boolean (unchecked checkboxes don't send data)
        $validated['show_invoice_number'] = $request->has('show_invoice_number');
        $validated['show_date'] = $request->has('show_date');
        $validated['show_cashier'] = $request->has('show_cashier');
        $validated['show_logo'] = $request->has('show_logo');
        $validated['show_barcode'] = $request->has('show_barcode');
        $validated['show_tax_details'] = $request->has('show_tax_details');
        $validated['show_item_codes'] = $request->has('show_item_codes');
        $validated['show_discounts'] = $request->has('show_discounts');

        ReceiptSetting::updateOrCreate(
            ['business_name' => $manager->business_name],
            $validated
        );

        return redirect()->back()->with('success', 'Receipt settings updated successfully.');
    }
}
