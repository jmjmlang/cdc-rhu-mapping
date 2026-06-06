<?php

namespace App\Http\Controllers\RHU;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CaseReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportVerificationController extends Controller
{
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
            'properties' => [
                'report_id'    => $report->id,
                'display_name' => preg_replace('/\s+(\S)\S*$/', ' $1.', $request->user()->name),
                'via'          => 'rhu',
            ],
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
            'properties' => [
                'report_id'    => $report->id,
                'display_name' => preg_replace('/\s+(\S)\S*$/', ' $1.', $request->user()->name),
                'via'          => 'rhu',
            ],
        ]);

        return back()->with('success', "Report #{$report->id} rejected.");
    }
}
