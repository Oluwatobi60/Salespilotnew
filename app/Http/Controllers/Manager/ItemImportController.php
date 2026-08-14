<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StandardItemImport;
use App\Imports\VariantItemImport;
use App\Exports\StandardItemTemplateExport;
use App\Exports\VariantItemTemplateExport;
use Illuminate\Support\Facades\Auth;

class ItemImportController extends Controller
{
    /**
     * Download the template for standard items.
     */
    public function downloadStandardTemplate()
    {
        return Excel::download(new StandardItemTemplateExport, 'standard_items_template.xlsx');
    }

    /**
     * Import standard items from an Excel file.
     */
    public function importStandard(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,csv,xls',
        ]);

        try {
            Excel::import(new StandardItemImport, $request->file('import_file'));
            return redirect()->back()->with('success', 'Standard items imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $messages = [];
            foreach ($failures as $failure) {
                $messages[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->back()->with('error', 'Import failed due to validation errors. ' . implode(' | ', $messages));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred during import: ' . $e->getMessage());
        }
    }

    /**
     * Download the template for variant items.
     */
    public function downloadVariantTemplate()
    {
        return Excel::download(new VariantItemTemplateExport, 'variant_items_template.xlsx');
    }

    /**
     * Import variant items from an Excel file.
     */
    public function importVariant(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,csv,xls',
        ]);

        try {
            Excel::import(new VariantItemImport, $request->file('import_file'));
            return redirect()->back()->with('success', 'Variant items imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $messages = [];
            foreach ($failures as $failure) {
                $messages[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->back()->with('error', 'Import failed due to validation errors. ' . implode(' | ', $messages));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred during import: ' . $e->getMessage());
        }
    }
}
