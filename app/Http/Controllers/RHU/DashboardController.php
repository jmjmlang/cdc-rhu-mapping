<?php

namespace App\Http\Controllers\RHU;

use App\Http\Controllers\Controller;
use App\Models\CaseReport;
use App\Models\CaseReportAction;
use App\Models\HealthCategory;
use App\Services\DssService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly DssService $dss) {}

    public function index(Request $request): View
    {
        // ── DSS analysis (reuses DssService — last 30 days, approved only) ─────
        $flat    = $this->dss->analyse();
        $grouped = $this->dss->groupByBarangay($flat);
        $summary = $this->dss->summary($flat);

        // ── Top morbidity — top 5 diseases by case count (last 30 days) ────────
        $topDiseases = CaseReport::approved()
            ->withinDays(30)
            ->with('healthCategory')
            ->select('health_category_id')
            ->selectRaw('SUM(number_of_cases) as total_cases')
            ->groupBy('health_category_id')
            ->orderByDesc('total_cases')
            ->take(5)
            ->get()
            ->map(fn ($r) => [
                'name'        => $r->healthCategory->name ?? '—',
                'total_cases' => $r->total_cases,
            ]);

        // ── Trend data: this month vs. last month per disease ───────────────────
        // Two aggregated queries (not N×2) keyed by health_category_id.
        $thisMonthStart = now()->startOfMonth()->toDateString();
        $lastMonthStart = now()->subMonth()->startOfMonth()->toDateString();
        $lastMonthEnd   = now()->subMonth()->endOfMonth()->toDateString();

        $categories = HealthCategory::orderBy('name')->get();

        $thisMonthSums = CaseReport::approved()
            ->where('report_date', '>=', $thisMonthStart)
            ->selectRaw('health_category_id, SUM(number_of_cases) as total')
            ->groupBy('health_category_id')
            ->pluck('total', 'health_category_id');

        $lastMonthSums = CaseReport::approved()
            ->whereBetween('report_date', [$lastMonthStart, $lastMonthEnd])
            ->selectRaw('health_category_id, SUM(number_of_cases) as total')
            ->groupBy('health_category_id')
            ->pluck('total', 'health_category_id');

        $trendLabels   = $categories->pluck('name')->values()->all();
        $thisMonthData = [];
        $lastMonthData = [];

        foreach ($categories as $cat) {
            $thisMonthData[] = (int) ($thisMonthSums[$cat->id] ?? 0);
            $lastMonthData[] = (int) ($lastMonthSums[$cat->id] ?? 0);
        }

        // ── Risk doughnut (reuses DssController palette) ────────────────────────
        $riskLabels = ['Low', 'Moderate', 'High', 'Critical'];
        $riskColors = [
            'rgba(107,114,128,0.75)', 'rgba(245,158,11,0.75)',
            'rgba(249,115,22,0.75)',  'rgba(239,68,68,0.75)',
        ];
        $riskCounts = [];
        foreach ($riskLabels as $level) {
            $riskCounts[] = $flat->where('risk_level', $level)->count();
        }

        // ── Pending reports for quick verification (last 30 days) ───────────────
        $pending = CaseReport::pending()
            ->withinDays(30)
            ->with(['user', 'barangay', 'healthCategory'])
            ->latest()
            ->paginate(10);

        $globalAnnouncements = CaseReportAction::allUsers()
            ->open()
            ->with(['author', 'caseReport.barangay', 'caseReport.healthCategory'])
            ->latest()
            ->take(5)
            ->get();

        return view('pages.rhu.dashboard', compact(
            'summary', 'grouped',
            'topDiseases',
            'trendLabels', 'thisMonthData', 'lastMonthData',
            'riskLabels', 'riskCounts', 'riskColors',
            'pending',
            'globalAnnouncements'
        ));
    }
}
