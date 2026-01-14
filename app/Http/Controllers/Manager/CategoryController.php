<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\StandardItem;
use App\Models\VariantItem;
use App\Models\BundleItem;

class CategoryController extends Controller
{
    public function all_category()
    {
        $categories = Category::all();

        // Count items for each category
        foreach ($categories as $category) {
            $standardCount = StandardItem::where('category', $category->id)->count();
            $variantCount = VariantItem::where('category', $category->id)->count();
            $bundleCount = BundleItem::where('category', $category->id)->count();

            $category->items_count = $standardCount + $variantCount + $bundleCount;
        }

        return view('manager.category.all_category', compact('categories'));
    }


    public function create_category(Request $request)
    {
        // Validate the request data
       $validatedata = $request->validate([
             'category_name' => 'unique:categories|max:100|min:5|required',
        ]);

        // Create a new category
        $category = Category::create($validatedata);
        \App\Helpers\ActivityLogger::log('create_category', 'Created category: ' . $category->category_name);


        // Redirect back with success message
        return redirect()->route('all_categories')->with('success', 'Category created successfully.');
    }

    public function edit_category($id)
    {
        $category = Category::findOrFail($id);
        return view('manager.category.edit_category', compact('category'));
    }

    public function update_category(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        // Validate the request data
        $validatedData = $request->validate([
            'category_name' => 'required|max:100|min:5|unique:categories,category_name,' . $category->id,
        ]);

        // Update the category
        $category->update($validatedData);
        \App\Helpers\ActivityLogger::log('update_category', 'Updated category: ' . $category->category_name);

        // Redirect back with success message
        return redirect()->route('all_categories')->with('success', 'Category updated successfully.');
    }


    public function delete_category($id)
    {
        $category = Category::findOrFail($id);

        // Check if any items are associated with this category
        $standardCount = StandardItem::where('category', $category->id)->count();
        $variantCount = VariantItem::where('category', $category->id)->count();
        $bundleCount = BundleItem::where('category', $category->id)->count();

        if ($standardCount > 0 || $variantCount > 0 || $bundleCount > 0) {
            return redirect()->route('all_categories')->with('error', 'Cannot delete category. There are items associated with this category.');
        }

        // Delete the category
        $categoryName = $category->category_name;
        $category->delete();
        \App\Helpers\ActivityLogger::log('delete_category', 'Deleted category: ' . $categoryName);

        // Redirect back with success message
        return redirect()->route('all_categories')->with('success', 'Category deleted successfully.');
    }
}
