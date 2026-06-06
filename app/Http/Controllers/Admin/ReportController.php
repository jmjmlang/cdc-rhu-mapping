<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barangay;
use App\Models\CaseReport;
use App\Models\HealthCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = CaseReport::with(['user', 'barangay', 'healthCategory', 'reviewer']);

        // Main table: approved only — pending has its own section
        $query->where('status', 'approved');

        // Filter by barangay
        if ($request->filled('barangay_id')) {
            $query->where('barangay_id', $request->barangay_id);
        }

        // Filter by health category
        if ($request->filled('health_category_id')) {
            $query->where('health_category_id', $request->health_category_id);
        }

        // Sorting
        $sortField = $request->query('sort', 'created_at');
        $sortDir   = $request->query('dir', 'desc');

        $allowedSorts = ['created_at', 'report_date', 'number_of_cases', 'status'];
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'created_at';
        }
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortField, $sortDir);

        $pendingReports = CaseReport::with(['user', 'barangay', 'healthCategory'])
            ->where('status', 'pending')
            ->latest('created_at')
            ->get();

        $reports = $query->paginate(20)->withQueryString();

        $barangays        = Barangay::orderBy('name')->get();
        $healthCategories = HealthCategory::orderBy('name')->get();

        // Status counts for the stat cards
        $pendingCount  = CaseReport::where('status', 'pending')->count();
        $approvedCount = CaseReport::where('status', 'approved')->count();
        $rejectedCount = CaseReport::withTrashed()
            ->where(function ($q) {
                $q->where('status', 'rejected')->orWhereNotNull('deleted_at');
            })->count();

        // Rejected/deleted — last 30 days, includes soft-deleted
        $rejected = CaseReport::withTrashed()
            ->where(function ($q) {
                $q->where('status', 'rejected')->orWhereNotNull('deleted_at');
            })
            ->withinDays(30)
            ->with(['user', 'barangay', 'healthCategory', 'reviewer'])
            ->latest()
            ->take(20)
            ->get();

        return view('pages.admin.reports', compact(
            'reports', 'pendingReports', 'barangays', 'healthCategories', 'sortField', 'sortDir', 'rejected',
            'pendingCount', 'approvedCount', 'rejectedCount'
        ));
    }
}
