<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch\Branch;
use App\Models\BranchInventory;
use App\Models\ProductVariant;
use App\Models\SellProduct;
use App\Models\StandardItem;
use App\Models\VariantItem;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AICopilotController extends Controller
{
    /**
     * Process Natural Language queries from staff/managers
     */
    public function query(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:500',
        ]);

        try {
            $user = Auth::user();
            
            // Handle staff user vs manager user
            if (Auth::guard('staff')->check()) {
                $staff = Auth::guard('staff')->user();
                $businessName = $staff->business_name;
                $creatorEmail = $staff->manager_email;
                $creator = \App\Models\User::where('email', $creatorEmail)->first();
                $managerId = $creator ? $creator->id : null;
            } else {
                $businessName = $user->business_name;
                if ($user->addby) {
                    $creator = \App\Models\User::where('email', $user->addby)->first();
                    $managerId = $creator ? $creator->id : $user->id;
                } else {
                    $managerId = $user->id;
                }
            }

            $query = trim($request->input('query'));
            $queryLower = strtolower($query);

            // Tokenize query for text searching
            $stopWords = ['do', 'we', 'have', 'any', 'left', 'in', 'the', 'of', 'for', 'a', 'an', 'and', 'is', 'are', 'summarize', 'show', 'list', 'about', 'with', 'from', 'at'];
            $words = array_filter(explode(' ', preg_replace('/[^\w\s]/', '', $queryLower)), function($word) use ($stopWords) {
                return strlen($word) > 2 && !in_array($word, $stopWords);
            });

            // 1. Query branches
            $branches = Branch::where('business_name', $businessName)
                ->orWhere('user_id', $managerId)
                ->get(['id', 'branch_name']);

            // 2. Query items matching query keywords
            $itemsQuery = StandardItem::where('business_name', $businessName);
            if (!empty($words)) {
                $itemsQuery->where(function($q) use ($words) {
                    foreach ($words as $word) {
                        $q->orWhere('item_name', 'like', '%' . $word . '%');
                        $q->orWhere('description', 'like', '%' . $word . '%');
                    }
                });
            }
            $standardItems = $itemsQuery->take(10)->get(['id', 'item_name', 'current_stock', 'selling_price']);

            // 3. Query variant items matching keywords
            $variantsQuery = ProductVariant::where('business_name', $businessName);
            if (!empty($words)) {
                $variantsQuery->where(function($q) use ($words) {
                    foreach ($words as $word) {
                        $q->orWhere('variant_name', 'like', '%' . $word . '%');
                    }
                });
            }
            $variantItems = $variantsQuery->with('variantItem:id,item_name')->take(10)->get(['id', 'variant_item_id', 'variant_name', 'current_stock', 'selling_price']);

            // 4. Query branch allocations
            $branchInventories = BranchInventory::where('business_name', $businessName)
                ->whereIn('item_id', array_merge(
                    $standardItems->pluck('id')->toArray(),
                    $variantItems->pluck('id')->toArray()
                ))
                ->with('branch:id,branch_name')
                ->get(['branch_id', 'item_id', 'item_type', 'current_quantity']);

            // 5. Query sales report context if query asks about sales, revenue, categories, or summaries
            $todaySalesContext = null;
            $salesKeywords = ['sale', 'revenue', 'sold', 'category', 'categories', 'today', 'report', 'summarize', 'summary'];
            $isSalesQuery = false;
            foreach ($salesKeywords as $keyword) {
                if (str_contains($queryLower, $keyword)) {
                    $isSalesQuery = true;
                    break;
                }
            }

            if ($isSalesQuery) {
                // Today's total sales transactions
                $sales = \App\Models\CartItem::where('business_name', $businessName)
                    ->where('status', 'completed')
                    ->whereDate('created_at', today())
                    ->get(['item_name', 'item_price', 'quantity', 'total', 'branch_name', 'created_at']);

                $totalRevenue = $sales->sum('total');
                $totalQuantity = $sales->sum('quantity');
                
                // Group by branch
                $byBranch = $sales->groupBy('branch_name')->map(fn($grp) => [
                    'revenue' => $grp->sum('total'),
                    'quantity' => $grp->sum('quantity'),
                    'transactions' => $grp->count()
                ]);

                // Group by item
                $byItem = $sales->groupBy('item_name')->map(fn($grp) => [
                    'revenue' => $grp->sum('total'),
                    'quantity' => $grp->sum('quantity')
                ])->sortByDesc('quantity')->take(5);

                $todaySalesContext = [
                    'total_revenue' => $totalRevenue,
                    'total_quantity_sold' => $totalQuantity,
                    'transaction_count' => $sales->count(),
                    'sales_by_branch' => $byBranch,
                    'top_selling_items' => $byItem
                ];
            }

            // Build Context Dictionary
            $context = [
                'business_name' => $businessName,
                'branches' => $branches->map(fn($b) => ['id' => $b->id, 'name' => $b->branch_name]),
                'matching_standard_items' => $standardItems->map(fn($i) => [
                    'name' => $i->item_name,
                    'main_warehouse_stock' => $i->current_stock,
                    'price' => $i->selling_price,
                    'branch_stocks' => $branchInventories->where('item_id', $i->id)->where('item_type', 'standard')->map(fn($bi) => [
                        'branch' => $bi->branch->branch_name ?? 'Unknown',
                        'stock' => $bi->current_quantity
                    ])->values()
                ]),
                'matching_variant_items' => $variantItems->map(fn($v) => [
                    'name' => ($v->variantItem->item_name ?? 'Item') . ' (' . $v->variant_name . ')',
                    'main_warehouse_stock' => $v->current_stock,
                    'price' => $v->selling_price,
                    'branch_stocks' => $branchInventories->where('item_id', $v->id)->where('item_type', 'variant')->map(fn($bi) => [
                        'branch' => $bi->branch->branch_name ?? 'Unknown',
                        'stock' => $bi->current_quantity
                    ])->values()
                ]),
                'today_sales_metrics' => $todaySalesContext,
                'current_time' => now()->toDateTimeString()
            ];

            // Formulate Gemini Prompt
            $contextJson = json_encode($context, JSON_PRETTY_PRINT);
            $prompt = "You are the SalesPilot AI POS Copilot, a helpful voice & text chatbot for store staff and managers.\n"
                    . "Here is the real-time context database details for the business \"{$businessName}\":\n"
                    . "{$contextJson}\n\n"
                    . "The user asked: \"{$query}\"\n\n"
                    . "Guidelines:\n"
                    . "- Answer the query directly and concisely in 2-3 sentences based ONLY on the context data.\n"
                    . "- Use HTML tags (like <strong>, <ul>, <li>) to structure lists, pricing, or numbers so they display beautifully on a screen.\n"
                    . "- If the user asks about stock, specify the stock level in the main warehouse and in specific branches if available.\n"
                    . "- If the user asks about today's sales or summaries, state the total revenue, quantities sold, and listing breakdowns.\n"
                    . "- If no matching records or data are found in the context, politely state: \"I couldn't find any records matching that in the catalog/sales history. Let me know if you want to try a different query.\"";

            $response = GeminiService::generateText($prompt);

            return response()->json([
                'success' => true,
                'response' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('AI Copilot Query Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
