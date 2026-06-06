<?php

namespace App\Http\Controllers;

use App\Models\CaseReport;
use App\Models\CaseReportAction;
use App\Services\CaseReportActionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaseReportActionController extends Controller
{
    public function __construct(private readonly CaseReportActionService $actions) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        abort_if(! $user || (! $user->isAdmin() && ! $user->isRhu()), 403);

        $status = $request->query('status', 'open');
        $allowedStatuses = ['open', 'completed', 'all'];
        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'open';
        }

        $actionsQuery = CaseReportAction::with([
            'author',
            'caseReport.barangay',
            'caseReport.healthCategory',
        ])->latest();

        if ($status !== 'all') {
            $actionsQuery->where('status', $status);
        }

        $actions = $actionsQuery->paginate(15)->withQueryString();

        $reportPairs = CaseReport::with(['barangay', 'healthCategory'])
            ->select('barangay_id', 'health_category_id')
            ->selectRaw('MAX(id) as case_report_id')
            ->selectRaw('SUM(number_of_cases) as total_cases')
            ->selectRaw('MAX(report_date) as latest_report_date')
            ->whereIn('status', ['pending', 'approved'])
            ->groupBy('barangay_id', 'health_category_id')
            ->get();

        $barangayOptions = $reportPairs
            ->pluck('barangay')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $openCount = CaseReportAction::open()->count();
        $citizenVisibleCount = CaseReportAction::citizenVisible()->count();
        $completedCount = CaseReportAction::where('status', 'completed')->count();

        return view('pages.case-actions.index', compact(
            'actions',
            'reportPairs',
            'barangayOptions',
            'status',
            'openCount',
            'citizenVisibleCount',
            'completedCount'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_if(! $user || (! $user->isAdmin() && ! $user->isRhu()), 403);

        $validated = $request->validate([
            'barangay_id'        => ['required', 'exists:barangays,id'],
            'health_category_id' => ['required', 'exists:health_categories,id'],
            'action_type'        => ['required', 'in:field_visit,health_education,referral,follow_up,supply_request,case_validation,public_advisory'],
            'priority'           => ['required', 'in:routine,urgent'],
            'audience'           => ['required', 'in:admin_only,citizen_visible,affected_citizens,all_users'],
            'message'            => ['required', 'string', 'min:10', 'max:1000'],
            'due_date'           => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $report = CaseReport::whereIn('status', ['pending', 'approved'])
            ->where('barangay_id', $validated['barangay_id'])
            ->where('health_category_id', $validated['health_category_id'])
            ->latest('report_date')
            ->latest('id')
            ->first();

        if (! $report) {
            return back()
                ->withInput()
                ->with('error', 'No active report exists for that barangay and disease pair.');
        }

        unset($validated['barangay_id'], $validated['health_category_id']);
        $validated['case_report_id'] = $report->id;

        $this->actions->create($validated, $user);

        return back()->with('success', 'Case coordination action added.');
    }

    public function complete(Request $request, CaseReportAction $caseAction): RedirectResponse
    {
        $user = $request->user();
        abort_if(! $user || (! $user->isAdmin() && ! $user->isRhu()), 403);

        if ($caseAction->status === 'completed') {
            return back()->with('error', 'This action is already completed.');
        }

        $this->actions->complete($caseAction, $user);

        return back()->with('success', 'Case coordination action marked complete.');
    }
}
