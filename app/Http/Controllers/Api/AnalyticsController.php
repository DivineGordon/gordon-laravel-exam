<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageAnalytic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->user()->clientPage;

        if (!$page) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        $period = $request->get('period', 'daily'); // daily, weekly, monthly

        $analytics = $this->getAnalyticsByPeriod($page->id, $period);
        $uniqueVisitors = $this->getUniqueVisitors($page->id, $period);
        $returningVisitors = $this->getReturningVisitors($page->id, $period);

        return response()->json([
            'total_views' => $analytics['total_views'],
            'views_by_date' => $analytics['views_by_date'],
            'unique_visitors' => $uniqueVisitors,
            'returning_visitors' => $returningVisitors,
            'period' => $period,
        ]);
    }

    private function getAnalyticsByPeriod($pageId, $period)
    {
        $dateFormat = match($period) {
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%W',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $daysBack = match($period) {
            'daily' => 30,
            'weekly' => 84, // 12 weeks
            'monthly' => 365,
            default => 30,
        };

        $viewsByDate = PageAnalytic::where('client_page_id', $pageId)
            ->where('visited_at', '>=', Carbon::now()->subDays($daysBack))
            ->select(
                DB::raw("strftime('$dateFormat', visited_at) as date"),
                DB::raw('COUNT(*) as views')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalViews = PageAnalytic::where('client_page_id', $pageId)->count();

        return [
            'total_views' => $totalViews,
            'views_by_date' => $viewsByDate,
        ];
    }

    private function getUniqueVisitors($pageId, $period)
    {
        $daysBack = match($period) {
            'daily' => 30,
            'weekly' => 84,
            'monthly' => 365,
            default => 30,
        };

        return PageAnalytic::where('client_page_id', $pageId)
            ->where('visited_at', '>=', Carbon::now()->subDays($daysBack))
            ->distinct('session_id')
            ->count('session_id');
    }

    private function getReturningVisitors($pageId, $period)
    {
        $daysBack = match($period) {
            'daily' => 30,
            'weekly' => 84,
            'monthly' => 365,
            default => 30,
        };

        // Visitors who have visited more than once
        $returningVisitors = PageAnalytic::where('client_page_id', $pageId)
            ->where('visited_at', '>=', Carbon::now()->subDays($daysBack))
            ->select('session_id', DB::raw('COUNT(*) as visit_count'))
            ->groupBy('session_id')
            ->having('visit_count', '>', 1)
            ->count();

        return $returningVisitors;
    }

    public function export(Request $request)
    {
        $page = $request->user()->clientPage;

        if (!$page) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        $analytics = PageAnalytic::where('client_page_id', $page->id)
            ->orderBy('visited_at', 'desc')
            ->get();

        $csv = "Date,Time,IP Address,User Agent,Referer\n";
        
        foreach ($analytics as $analytic) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s\n",
                $analytic->visited_at->format('Y-m-d'),
                $analytic->visited_at->format('H:i:s'),
                $analytic->visitor_ip,
                str_replace(',', ';', $analytic->user_agent),
                $analytic->referer ?? 'Direct'
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="analytics-' . date('Y-m-d') . '.csv"');
    }
}