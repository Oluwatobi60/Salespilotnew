<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Supplier;

class SupplierController extends Controller
{
   public function suppliers()
    {
        $manager = Auth::user();
        $businessName = $manager->business_name;

        $suppliers = Supplier::where('business_name', $businessName)->paginate(10);
        return view('manager.supplier.supplier', compact('suppliers'));
    }

    public function create_supplier(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        // Get manager information
        $manager = Auth::user();
        $managerName = trim(($manager->firstname ?? '') . ' ' . ($manager->othername ?? '') . ' ' . ($manager->surname ?? ''));

        // Add manager info to validated data
        $validatedData['business_name'] = $manager->business_name;
        $validatedData['manager_name'] = $managerName;
        $validatedData['manager_email'] = $manager->email;

        // Create a new supplier
        $supplier = Supplier::create($validatedData);

        // Check if the request expects JSON (AJAX request)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Supplier created successfully',
                'supplier' => [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'email' => $supplier->email,
                    'contact_person' => $supplier->contact_person,
                    'phone' => $supplier->phone,
                    'address' => $supplier->address
                ]
            ], 201);
        }

        // Redirect back with success message
        return redirect()->route('manager.suppliers')->with('success', 'Supplier added successfully.');
    }

    public function edit_supplier($id)
    {
        $manager = Auth::user();
        $businessName = $manager->business_name;
        
        // ✅ SECURITY: Verify supplier belongs to manager's business
        $supplier = Supplier::where('business_name', $businessName)
            ->findOrFail($id);
        return view('manager.supplier.edit_supplier', compact('supplier'));
    }

    public function update_supplier(Request $request, $id)
    {
        $manager = Auth::user();
        $businessName = $manager->business_name;
        
        // ✅ SECURITY: Verify supplier belongs to manager's business
        $supplier = Supplier::where('business_name', $businessName)
            ->findOrFail($id);

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email,' . $supplier->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        // Update the supplier
        $supplier->update($validatedData);

        // Check if the request expects JSON (AJAX request)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Supplier updated successfully',
                'supplier' => [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'email' => $supplier->email,
                    'contact_person' => $supplier->contact_person,
                    'phone' => $supplier->phone,
                    'address' => $supplier->address
                ]
            ], 200);
        }

        // Redirect back with success message
        return redirect()->route('manager.suppliers')->with('success', 'Supplier updated successfully.');
    }


    public function delete_supplier($id)
    {
        $manager = Auth::user();
        $businessName = $manager->business_name;
        
        // ✅ SECURITY: Verify supplier belongs to manager's business
        $supplier = Supplier::where('business_name', $businessName)
            ->findOrFail($id);
        $supplier->delete();

        // Check if the request expects JSON (AJAX request)
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Supplier deleted successfully'
            ], 200);
        }

        // Redirect back with success message
        return redirect()->route('manager.suppliers')->with('success', 'Supplier deleted successfully.');
    }
}
