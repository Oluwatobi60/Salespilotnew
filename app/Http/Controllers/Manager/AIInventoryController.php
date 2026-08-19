<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AIInventoryController extends Controller
{
    /**
     * Suggest Category based on item name and description
     */
    public function suggestCategory(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $user = Auth::user();
            // Get manager's business name
            if ($user->addby) {
                $creator = User::where('email', $user->addby)->first();
                $businessName = $creator ? $creator->business_name : $user->business_name;
            } else {
                $businessName = $user->business_name;
            }

            // Retrieve all available categories
            $categories = Category::where('business_name', $businessName)->get();

            if ($categories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No categories defined in your business catalog yet. Please create at least one category first.'
                ]);
            }

            // Format category options for prompt
            $categoryOptions = [];
            foreach ($categories as $cat) {
                $categoryOptions[] = [
                    'id' => $cat->id,
                    'name' => $cat->category_name
                ];
            }
            $categoriesJson = json_encode($categoryOptions);

            $itemName = $request->input('item_name');
            $description = $request->input('description', '');

            $prompt = "You are an AI assistant for a retail POS and inventory management system named SalesPilot.\n"
                    . "Given a product name: \"{$itemName}\" and description: \"{$description}\".\n"
                    . "Here is a list of available categories with their IDs: {$categoriesJson}.\n"
                    . "Determine which of these categories fits the product best.\n"
                    . "Return ONLY the exact category ID (integer) from the list. If none fit, return \"null\".\n"
                    . "Do not write any introductory text, concluding text, or markdown code blocks. Just return the raw ID or null.";

            $response = GeminiService::generateText($prompt);
            $suggestedId = trim($response);

            if (is_numeric($suggestedId)) {
                $categoryId = (int) $suggestedId;
                // Double check it exists in the user's categories
                $exists = $categories->contains('id', $categoryId);
                if ($exists) {
                    return response()->json([
                        'success' => true,
                        'category_id' => $categoryId
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'AI could not confidently match this product to any of your existing categories.'
            ]);

        } catch (\Exception $e) {
            Log::error('AI Suggest Category Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate product description based on item name and category
     */
    public function generateDescription(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'category_id' => 'nullable|integer',
        ]);

        try {
            $itemName = $request->input('item_name');
            $categoryId = $request->input('category_id');
            $categoryName = 'Uncategorized';

            if ($categoryId) {
                $cat = Category::find($categoryId);
                if ($cat) {
                    $categoryName = $cat->category_name;
                }
            }

            $prompt = "You are an expert sales copywriter for a POS and inventory system named SalesPilot.\n"
                    . "Write an engaging, premium, and SEO-friendly product description for the following product:\n"
                    . "Product Name: \"{$itemName}\"\n"
                    . "Product Category: \"{$categoryName}\"\n"
                    . "Requirements:\n"
                    . "- Keep it concise (around 2-3 sentences max).\n"
                    . "- Make it persuasive, professional, and highlight general utility.\n"
                    . "- Return ONLY the generated description without any introductory, concluding text, or markdown quotes. Just return the description text directly.";

            $description = GeminiService::generateText($prompt);

            return response()->json([
                'success' => true,
                'description' => $description
            ]);

        } catch (\Exception $e) {
            Log::error('AI Generate Description Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recommend Retail Selling Price based on cost price and category
     */
    public function recommendPrice(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'category_id' => 'nullable|integer',
        ]);

        try {
            $itemName = $request->input('item_name');
            $costPrice = $request->input('cost_price');
            $categoryId = $request->input('category_id');
            $categoryName = 'Uncategorized';

            if ($categoryId) {
                $cat = Category::find($categoryId);
                if ($cat) {
                    $categoryName = $cat->category_name;
                }
            }

            $prompt = "You are a retail pricing strategist assistant for SalesPilot.\n"
                    . "Product Name: \"{$itemName}\"\n"
                    . "Cost Price (what the business paid): {$costPrice}\n"
                    . "Category: \"{$categoryName}\"\n\n"
                    . "Analyze the cost price and category to suggest a recommended retail selling price. Guidelines:\n"
                    . "- Use a standard retail markup appropriate for the category (typically 15% to 50% profit margin).\n"
                    . "- Provide a recommended retail price (higher than cost price).\n"
                    . "- Provide the calculated profit margin percentage based on your suggested price.\n"
                    . "- Provide a brief 1-sentence business justification.\n\n"
                    . "Return ONLY a valid JSON object matching this structure (no markdown tags, no ```json, no extra text):\n"
                    . "{\n"
                    . "  \"recommended_price\": 129.99,\n"
                    . "  \"margin_percentage\": 30.0,\n"
                    . "  \"justification\": \"A standard 30% markup is optimal for Footwear to cover storage costs and yield steady profits.\"\n"
                    . "}";

            $response = GeminiService::generateText($prompt);
            
            // Safe JSON parse
            // Remove markdown code blocks if any got leaked
            $responseClean = trim(preg_replace('/^```(?:json)?|```$/i', '', trim($response)));
            $data = json_decode($responseClean, true);

            if ($data === null || !isset($data['recommended_price']) || !isset($data['margin_percentage'])) {
                Log::error('AI Failed to Suggest Valid Price JSON', ['response' => $response]);
                
                // Fallback suggestion (e.g. 30% profit margin)
                $recommendedPrice = round($costPrice * 1.3, 2);
                return response()->json([
                    'success' => true,
                    'recommended_price' => $recommendedPrice,
                    'margin_percentage' => 23.0,
                    'justification' => 'Calculated using a default fallback 30% markup because AI response could not be parsed.'
                ]);
            }

            return response()->json(array_merge(['success' => true], $data));

        } catch (\Exception $e) {
            Log::error('AI Recommend Price Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
