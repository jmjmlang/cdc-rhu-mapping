<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\CaseReport;
use App\Models\CaseReportAction;
use App\Models\HealthCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isRhu()) {
            return redirect()->route('rhu.dashboard');
        }

        if ($user->isAdmin()) {
            // Pending — only last 30 days, for dashboard box
            $pending = CaseReport::pending()
                ->withinDays(30)
                ->with(['user', 'barangay', 'healthCategory'])
                ->latest()
                ->paginate(15);

            // Recently approved — last 30 days only for dashboard box
            $approved = CaseReport::approved()
                ->withinDays(30)
                ->with(['user', 'barangay', 'healthCategory'])
                ->latest()
                ->take(10)
                ->get();

            $pendingCount  = CaseReport::pending()->count();
            $approvedCount = CaseReport::approved()->count();
            $totalCases    = CaseReport::approved()->sum('number_of_cases');

            // Malnutrition-specific case total (approved, any time)
            $malnutritionId    = HealthCategory::where('name', 'Malnutrition')->value('id');
            $malnutritionCases = $malnutritionId
                ? CaseReport::approved()->where('health_category_id', $malnutritionId)->sum('number_of_cases')
                : 0;

            $barangays        = Barangay::orderBy('name')->get();
            $healthCategories = HealthCategory::orderBy('name')->get();
            $globalAnnouncements = CaseReportAction::allUsers()
                ->open()
                ->with(['author', 'caseReport.barangay', 'caseReport.healthCategory'])
                ->latest()
                ->take(5)
                ->get();

            return view('pages.admin.dashboard', compact(
                'pending', 'approved',
                'pendingCount', 'approvedCount',
                'totalCases', 'malnutritionCases', 'barangays', 'healthCategories',
                'globalAnnouncements'
            ));
        }

        // Citizen — include soft-deleted reports so citizen can see deletion reason
        $reports = $user->caseReports()
            ->withTrashed()
            ->with(['barangay', 'healthCategory'])
            ->latest()
            ->paginate(10);

        $barangays        = Barangay::orderBy('name')->get();
        $healthCategories = HealthCategory::orderBy('name')->get();

        // Own report status summary
        $ownTotal    = $user->caseReports()->count();
        $ownApproved = $user->caseReports()->approved()->count();
        $ownPending  = $user->caseReports()->pending()->count();

        // Top diseases in the municipality (approved, last 30 days) — for awareness banner
        $municipalTopDiseases = CaseReport::approved()
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

        $municipalTotalCases = CaseReport::approved()->withinDays(30)->sum('number_of_cases');

        $affectedBarangayIds = $user->caseReports()
            ->select('barangay_id')
            ->pluck('barangay_id')
            ->push($user->barangay_id)
            ->filter()
            ->unique()
            ->values();

        $citizenAnnouncements = CaseReportAction::query()
            ->open()
            ->with(['author', 'caseReport.barangay', 'caseReport.healthCategory'])
            ->where(function ($query) use ($affectedBarangayIds) {
                $query->where('audience', 'all_users')
                    ->orWhere(function ($affectedQuery) use ($affectedBarangayIds) {
                        $affectedQuery
                            ->affectedCitizenVisible()
                            ->whereHas('caseReport', fn ($caseQuery) => $caseQuery->whereIn('barangay_id', $affectedBarangayIds));
                    });
            })
            ->latest()
            ->take(5)
            ->get();

        return view('pages.citizen.dashboard', compact(
            'reports', 'barangays', 'healthCategories',
            'ownTotal', 'ownApproved', 'ownPending',
            'municipalTopDiseases', 'municipalTotalCases',
            'citizenAnnouncements'
        ));
    }

    public function healthGuide(): View
    {
        $healthCategories = HealthCategory::orderBy('name')->get();
        return view('pages.citizen.health-guide', compact('healthCategories'));
    }
}
