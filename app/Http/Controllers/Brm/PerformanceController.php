<?php

namespace App\Http\Controllers\Brm;

use App\Http\Controllers\Controller;
use App\Models\Brm;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class PerformanceController extends Controller
{
    /**
     * Show the BRM performance dashboard
     */
    public function index()
    {
        $brm = Auth::guard('brms')->user();

        // Get current month and last month dates
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // ===== CURRENT MONTH STATS =====
        $totalCustomers = $brm->customers()->count();
        
        // Count conversions (customers with subscriptions)
        $thisMonthConversions = $brm->customers()
            ->whereHas('subscriptions')
            ->count();
        
        // Revenue from commissions this month
        $thisMonthRevenue = $brm->commissions()
            ->where('status', '!=', 'rejected')
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('commission_amount');

        // Conversion rate
        $conversionRate = $totalCustomers > 0 
            ? round(($thisMonthConversions / $totalCustomers) * 100, 1) 
            : 0;

        // ===== LAST MONTH STATS (for comparison) =====
        $lastMonthConversions = $brm->customers()
            ->whereHas('subscriptions', function($q) {
                $q->whereBetween('created_at', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ]);
            })
            ->count();

        $lastMonthRevenue = $brm->commissions()
            ->where('status', '!=', 'rejected')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('commission_amount');

        // ===== TRENDS =====
        $conversionTrend = $this->calculateTrend($thisMonthConversions, $lastMonthConversions);
        $revenueTrend = $this->calculateTrend($thisMonthRevenue, $lastMonthRevenue);

        // ===== MONTHLY PERFORMANCE DATA (for chart) =====
        $monthlyPerformance = $this->getMonthlyPerformance($brm);

        // ===== LEADERBOARD =====
        $leaderboard = $this->getLeaderboard();

        // ===== ACHIEVEMENTS =====
        $achievements = $this->getAchievements($brm, $totalCustomers, $thisMonthConversions, $thisMonthRevenue, $conversionRate);

        return view('brm.performance.index', [
            'totalCustomers' => $totalCustomers,
            'thisMonthConversions' => $thisMonthConversions,
            'thisMonthRevenue' => $thisMonthRevenue,
            'conversionRate' => $conversionRate,
            'conversionTrend' => $conversionTrend,
            'revenueTrend' => $revenueTrend,
            'monthlyPerformance' => $monthlyPerformance,
            'leaderboard' => $leaderboard,
            'achievements' => $achievements,
            'brm' => $brm,
        ]);
    }

    /**
     * Calculate percentage trend between two periods
     */
    private function calculateTrend($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Get monthly performance data for the last 12 months
     */
    private function getMonthlyPerformance($brm)
    {
        $months = [];
        $revenue = [];
        $conversions = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->startOfMonth();
            $monthEnd = $month->endOfMonth();

            $months[] = $month->format('M Y');

            // Get revenue for this month
            $monthRevenue = $brm->commissions()
                ->where('status', '!=', 'rejected')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('commission_amount');
            $revenue[] = (float) $monthRevenue;

            // Get conversions for this month
            $monthConversions = $brm->customers()
                ->whereHas('subscriptions', function($q) use ($monthStart, $monthEnd) {
                    $q->whereBetween('created_at', [$monthStart, $monthEnd]);
                })
                ->count();
            $conversions[] = $monthConversions;
        }

        return [
            'months' => $months,
            'revenue' => $revenue,
            'conversions' => $conversions,
        ];
    }

    /**
     * Get BRM leaderboard (top 5 by total commission)
     */
    private function getLeaderboard()
    {
        return Brm::with('commissions')
            ->get()
            ->map(function($brm) {
                $totalCommission = $brm->commissions()
                    ->where('status', '!=', 'rejected')
                    ->sum('commission_amount');
                $customerCount = $brm->customers()->count();
                $conversionRate = $customerCount > 0 
                    ? round(($brm->customers()->whereHas('subscriptions')->count() / $customerCount) * 100, 1)
                    : 0;

                return [
                    'id' => $brm->id,
                    'name' => $brm->name,
                    'totalCommission' => $totalCommission,
                    'customers' => $customerCount,
                    'conversionRate' => $conversionRate,
                ];
            })
            ->sortByDesc('totalCommission')
            ->take(5)
            ->values();
    }

    /**
     * Get achievements/badges based on BRM performance
     */
    private function getAchievements($brm, $totalCustomers, $conversions, $revenue, $conversionRate)
    {
        $achievements = [];
        $unlockedCount = 0;

        // Achievement 1: Top Performer (Rank #1)
        $rank = Brm::get()
            ->map(function($b) {
                return [
                    'id' => $b->id,
                    'commission' => $b->commissions()->where('status', '!=', 'rejected')->sum('commission_amount'),
                ];
            })
            ->sortByDesc('commission')
            ->search(function($item) use ($brm) {
                return $item['id'] == $brm->id;
            });

        if ($rank === 0) {
            $achievements[] = [
                'title' => 'Top Performer',
                'description' => 'Rank #1 This Month',
                'icon' => 'bi-trophy-fill',
                'gradient' => 'linear-gradient(135deg, #f6d365 0%, #fda085 100%)',
                'unlocked' => true,
            ];
            $unlockedCount++;
        }

        // Achievement 2: Century Club (100+ customers)
        if ($totalCustomers >= 100) {
            $achievements[] = [
                'title' => '100 Customers',
                'description' => 'Century Club',
                'icon' => 'bi-star-fill',
                'gradient' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                'unlocked' => true,
            ];
            $unlockedCount++;
        } else {
            $achievements[] = [
                'title' => '100 Customers',
                'description' => 'Century Club (' . $totalCustomers . '/100)',
                'icon' => 'bi-star-fill',
                'gradient' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                'unlocked' => false,
            ];
        }

        // Achievement 3: Fast Closer (20+ deals in a month)
        $thisMonthDeals = $brm->commissions()
            ->where('status', '!=', 'rejected')
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->count();

        if ($thisMonthDeals >= 20) {
            $achievements[] = [
                'title' => 'Fast Closer',
                'description' => '20 Deals in a Month',
                'icon' => 'bi-lightning-fill',
                'gradient' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                'unlocked' => true,
            ];
            $unlockedCount++;
        } else {
            $achievements[] = [
                'title' => 'Fast Closer',
                'description' => '20 Deals in a Month (' . $thisMonthDeals . '/20)',
                'icon' => 'bi-lightning-fill',
                'gradient' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                'unlocked' => false,
            ];
        }

        // Achievement 4: Growth Leader (25%+ growth)
        $lastMonthRevenue = $brm->commissions()
            ->where('status', '!=', 'rejected')
            ->whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->sum('commission_amount');

        $growthRate = $lastMonthRevenue > 0 
            ? round((($revenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        if ($growthRate >= 25) {
            $achievements[] = [
                'title' => 'Growth Leader',
                'description' => '+25% Month Growth',
                'icon' => 'bi-graph-up-arrow',
                'gradient' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                'unlocked' => true,
            ];
            $unlockedCount++;
        } else {
            $achievements[] = [
                'title' => 'Growth Leader',
                'description' => '+25% Month Growth (' . $growthRate . '%)',
                'icon' => 'bi-graph-up-arrow',
                'gradient' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                'unlocked' => false,
            ];
        }

        // Achievement 5: Premium Seller (10+ subscriptions)
        if ($conversions >= 10) {
            $achievements[] = [
                'title' => 'Premium Seller',
                'description' => '10 Enterprise Deals',
                'icon' => 'bi-gem',
                'gradient' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                'unlocked' => true,
            ];
            $unlockedCount++;
        } else {
            $achievements[] = [
                'title' => 'Premium Seller',
                'description' => '10 Enterprise Deals (' . $conversions . '/10)',
                'icon' => 'bi-gem',
                'gradient' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                'unlocked' => false,
            ];
        }

        // Achievement 6: Hot Streak (7 consecutive days with conversions)
        $hotStreak = $this->checkHotStreak($brm);
        if ($hotStreak) {
            $achievements[] = [
                'title' => 'Hot Streak',
                'description' => '7 Days Consecutive',
                'icon' => 'bi-fire',
                'gradient' => 'linear-gradient(135deg, #30cfd0 0%, #330867 100%)',
                'unlocked' => true,
            ];
            $unlockedCount++;
        } else {
            $achievements[] = [
                'title' => 'Hot Streak',
                'description' => '7 Days Consecutive',
                'icon' => 'bi-fire',
                'gradient' => 'linear-gradient(135deg, #30cfd0 0%, #330867 100%)',
                'unlocked' => false,
            ];
        }

        return [
            'list' => $achievements,
            'unlockedCount' => $unlockedCount,
            'totalCount' => count($achievements),
        ];
    }

    /**
     * Check if BRM has 7 consecutive days with conversions
     */
    private function checkHotStreak($brm)
    {
        $consecutiveDays = 0;
        $currentDate = Carbon::now();

        for ($i = 0; $i < 30; $i++) {
            $date = $currentDate->copy()->subDays($i)->startOfDay();
            $hasConversion = $brm->commissions()
                ->where('status', '!=', 'rejected')
                ->whereBetween('created_at', [$date, $date->copy()->endOfDay()])
                ->exists();

            if ($hasConversion) {
                $consecutiveDays++;
                if ($consecutiveDays >= 7) {
                    return true;
                }
            } else {
                $consecutiveDays = 0;
            }
        }

        return false;
    }
}
