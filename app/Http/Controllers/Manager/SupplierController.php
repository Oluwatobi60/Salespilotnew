<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
   public function suppliers()
    {
        $suppliers = Supplier::all();
        return view('manager.supplier.supplier', compact('suppliers'));
    }

    public function create_supplier(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:suppliers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        // Create a new supplier
        Supplier::create($validatedData);

        // Redirect back with success message
        return redirect()->route('manager.suppliers')->with('success', 'Supplier added successfully.');
    }

    public function edit_supplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('manager.supplier.edit_supplier', compact('supplier'));
    }

    public function update_supplier(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:suppliers,email,' . $supplier->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        // Update the supplier
        $supplier->update($validatedData);

        // Redirect back with success message
        return redirect()->route('manager.suppliers')->with('success', 'Supplier updated successfully.');
    }


    public function delete_supplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        // Redirect back with success message
        return redirect()->route('manager.suppliers')->with('success', 'Supplier deleted successfully.');
    }
}
