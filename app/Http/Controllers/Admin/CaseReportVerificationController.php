<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CaseReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CaseReportVerificationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'barangay_id'        => ['required', 'exists:barangays,id'],
            'health_category_id' => ['required', 'exists:health_categories,id'],
            'number_of_cases'    => ['required', 'integer', 'min:1'],
            'report_date'        => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        CaseReport::create(array_merge($validated, [
            'user_id'     => $request->user()->id,
            'status'      => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]));

        return back()->with('success', 'Report created and auto-approved.');
    }

    public function approve(Request $request, CaseReport $report): RedirectResponse
    {
        if ($report->status !== 'pending') {
            return back()->with('error', "Report #{$report->id} is already {$report->status}.");
        }

        $report->update([
            'status'      => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        ActivityLog::create([
            'user_id'    => $request->user()->id,
            'action'     => 'report_approved',
            'properties' => ['report_id' => $report->id, 'display_name' => preg_replace('/\s+(\S)\S*$/', ' $1.', $request->user()->name)],
        ]);

        return back()->with('success', "Report #{$report->id} approved.");
    }

    public function reject(Request $request, CaseReport $report): RedirectResponse
    {
        if ($report->status !== 'pending') {
            return back()->with('error', "Report #{$report->id} is already {$report->status}.");
        }

        $report->update([
            'status'      => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        ActivityLog::create([
            'user_id'    => $request->user()->id,
            'action'     => 'report_rejected',
            'properties' => ['report_id' => $report->id, 'display_name' => preg_replace('/\s+(\S)\S*$/', ' $1.', $request->user()->name)],
        ]);

        return back()->with('success', "Report #{$report->id} rejected.");
    }

    public function update(Request $request, CaseReport $report): RedirectResponse
    {
        $validated = $request->validate([
            'barangay_id'        => ['required', 'exists:barangays,id'],
            'health_category_id' => ['required', 'exists:health_categories,id'],
            'number_of_cases'    => ['required', 'integer', 'min:1'],
            'report_date'        => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $report->update($validated);

        ActivityLog::create([
            'user_id'    => $request->user()->id,
            'action'     => 'report_edited',
            'properties' => ['report_id' => $report->id, 'display_name' => preg_replace('/\s+(\S)\S*$/', ' $1.', $request->user()->name)],
        ]);

        return back()->with('success', "Report #{$report->id} updated.");
    }

    public function destroy(Request $request, CaseReport $report): RedirectResponse
    {
        $request->validate([
            'deletion_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $report->update(['deletion_reason' => $request->deletion_reason]);
        $reportId = $report->id;
        $report->delete();

        ActivityLog::create([
            'user_id'    => $request->user()->id,
            'action'     => 'report_deleted',
            'properties' => ['report_id' => $reportId, 'display_name' => preg_replace('/\s+(\S)\S*$/', ' $1.', $request->user()->name)],
        ]);

        return back()->with('success', "Report #{$reportId} has been deleted.");
    }
}
