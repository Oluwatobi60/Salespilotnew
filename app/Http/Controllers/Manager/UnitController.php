<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Unit;

class UnitController extends Controller
{
    /**
     * Display all units
     */
    public function all_units()
    {
        $manager = Auth::user();
        $businessName = $manager->business_name;

        $units = Unit::where('business_name', $businessName)
                     ->orderBy('created_at', 'desc')
                     ->get();

        return view('manager.units.all_units', compact('units'));
    }

    /**
     * Create a new unit
     */
    public function create_unit(Request $request)
    {
        // Get manager information
        $manager = Auth::user();

        // Validate the request data - unit name must be unique per business
        $validatedata = $request->validate([
            'name' => [
                'required',
                'min:2',
                'max:50',
                function ($attribute, $value, $fail) use ($manager) {
                    $exists = Unit::where('business_name', $manager->business_name)
                        ->where('name', $value)
                        ->exists();

                    if ($exists) {
                        $fail('This unit name already exists for your business.');
                    }
                }
            ],
            'abbreviation' => [
                'required',
                'min:1',
                'max:10',
                function ($attribute, $value, $fail) use ($manager) {
                    $exists = Unit::where('business_name', $manager->business_name)
                        ->where('abbreviation', $value)
                        ->exists();

                    if ($exists) {
                        $fail('This abbreviation already exists for your business.');
                    }
                }
            ],
        ]);

        $managerName = trim(($manager->firstname ?? '') . ' ' . ($manager->othername ?? '') . ' ' . ($manager->surname ?? ''));

        // Add manager info to validated data
        $validatedata['business_name'] = $manager->business_name;
        $validatedata['manager_name'] = $managerName;
        $validatedata['manager_email'] = $manager->email;
        $validatedata['is_custom'] = true;

        // Create a new unit
        $unit = Unit::create($validatedata);

        // Log activity if helper exists
        if (class_exists('\App\Helpers\ActivityLogger')) {
            \App\Helpers\ActivityLogger::log('create_unit', 'Created unit: ' . $unit->name);
        }

        // Check if the request expects JSON (AJAX request)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Unit created successfully',
                'unit' => [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'abbreviation' => $unit->abbreviation
                ]
            ], 201);
        }

        // Redirect back with success message
        return redirect()->back()->with('success', 'Unit created successfully.');
    }

    /**
     * Update an existing unit
     */
    public function update_unit(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);
        $manager = Auth::user();

        // Validate the request data - unit name and abbreviation must be unique per business (excluding current unit)
        $validatedata = $request->validate([
            'name' => [
                'required',
                'min:2',
                'max:50',
                function ($attribute, $value, $fail) use ($manager, $id) {
                    $exists = Unit::where('business_name', $manager->business_name)
                        ->where('name', $value)
                        ->where('id', '!=', $id)
                        ->exists();

                    if ($exists) {
                        $fail('This unit name already exists for your business.');
                    }
                }
            ],
            'abbreviation' => [
                'required',
                'min:1',
                'max:10',
                function ($attribute, $value, $fail) use ($manager, $id) {
                    $exists = Unit::where('business_name', $manager->business_name)
                        ->where('abbreviation', $value)
                        ->where('id', '!=', $id)
                        ->exists();

                    if ($exists) {
                        $fail('This abbreviation already exists for your business.');
                    }
                }
            ],
        ]);

        // Update the unit
        $unit->update($validatedata);

        // Log activity if helper exists
        if (class_exists('\App\Helpers\ActivityLogger')) {
            \App\Helpers\ActivityLogger::log('update_unit', 'Updated unit: ' . $unit->name);
        }

        // Redirect back with success message
        return redirect()->route('manager.units')->with('success', 'Unit updated successfully.');
    }

    /**
     * Delete a unit
     */
    public function delete_unit($id)
    {
        $unit = Unit::findOrFail($id);

        // Check if unit is used by any items before deleting
        // You might want to add this check based on your database structure
        // For now, we'll allow deletion

        // Log activity if helper exists
        if (class_exists('\App\Helpers\ActivityLogger')) {
            \App\Helpers\ActivityLogger::log('delete_unit', 'Deleted unit: ' . $unit->name);
        }

        $unit->delete();

        return redirect()->route('manager.units')->with('success', 'Unit deleted successfully.');
    }
}
