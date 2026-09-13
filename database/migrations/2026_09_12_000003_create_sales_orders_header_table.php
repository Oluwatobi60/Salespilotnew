<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->string('session_id')->nullable()->index();
            $table->string('business_name')->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('staff_id')->nullable()->constrained('staffs')->onDelete('set null');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->string('branch_name')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('add_customers')->onDelete('set null');
            $table->string('customer_name')->default('Walk-in Customer');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_total', 15, 2)->default(0);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->string('payment_method')->default('cash');
            $table->string('status')->default('completed');
            $table->decimal('items_count', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable()->index();
            $table->timestamps();

            $table->index(['business_name', 'status', 'created_at'], 'sales_biz_status_created_idx');
            $table->index(['branch_id', 'status'], 'sales_branch_status_idx');
        });

        // Backfill historical sales from completed cart items
        if (Schema::hasTable('cart_items')) {
            $completedCarts = DB::table('cart_items')
                ->where('status', 'completed')
                ->whereNotNull('receipt_number')
                ->where('receipt_number', '!=', '')
                ->select(
                    'receipt_number',
                    DB::raw('MAX(session_id) as session_id'),
                    DB::raw('MAX(business_name) as business_name'),
                    DB::raw('MAX(user_id) as user_id'),
                    DB::raw('MAX(staff_id) as staff_id'),
                    DB::raw('MAX(branch_id) as branch_id'),
                    DB::raw('MAX(branch_name) as branch_name'),
                    DB::raw('MAX(customer_id) as customer_id'),
                    DB::raw('MAX(customer_name) as customer_name'),
                    DB::raw('SUM(subtotal) as subtotal'),
                    DB::raw('SUM(discount) as discount_total'),
                    DB::raw('SUM(total) as grand_total'),
                    DB::raw('SUM(quantity) as items_count'),
                    DB::raw('MIN(created_at) as created_at'),
                    DB::raw('MAX(updated_at) as updated_at')
                )
                ->groupBy('receipt_number')
                ->get();

            foreach ($completedCarts as $cart) {
                if (! empty($cart->receipt_number) && ! empty($cart->business_name)) {
                    DB::table('sales')->insertOrIgnore([
                        'receipt_number' => $cart->receipt_number,
                        'session_id' => $cart->session_id,
                        'business_name' => $cart->business_name,
                        'user_id' => $cart->user_id,
                        'staff_id' => $cart->staff_id,
                        'branch_id' => $cart->branch_id,
                        'branch_name' => $cart->branch_name,
                        'customer_id' => $cart->customer_id,
                        'customer_name' => $cart->customer_name ?: 'Walk-in Customer',
                        'subtotal' => $cart->subtotal ?: 0,
                        'discount_total' => $cart->discount_total ?: 0,
                        'tax_total' => 0,
                        'grand_total' => $cart->grand_total ?: 0,
                        'payment_method' => 'cash',
                        'status' => 'completed',
                        'items_count' => $cart->items_count ?: 0,
                        'completed_at' => $cart->created_at ?: now(),
                        'created_at' => $cart->created_at ?: now(),
                        'updated_at' => $cart->updated_at ?: now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
