<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CaseReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CaseReportController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'barangay_id'        => ['required', 'exists:barangays,id'],
            'health_category_id' => ['required', 'exists:health_categories,id'],
            'number_of_cases'    => ['required', 'integer', 'min:1', 'max:9999'],
            'report_date'        => ['required', 'date', 'before_or_equal:today'],
            'notes'              => ['nullable', 'string', 'max:1000'],
            'symptoms'           => ['nullable', 'string', 'max:1000'],
            'patient_name'       => ['nullable', 'string', 'max:191'],
            'patient_age'        => ['nullable', 'integer', 'min:0', 'max:150'],
            'patient_gender'     => ['nullable', 'in:male,female'],
            'patient_birthdate'  => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending';

        $report = CaseReport::create($validated);

        $shortName = preg_replace('/\s+(\S)\S*$/', ' $1.', $request->user()->name);

        ActivityLog::create([
            'user_id'    => $request->user()->id,
            'action'     => 'report_submitted',
            'properties' => ['report_id' => $report->id, 'display_name' => $shortName],
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Report submitted. Awaiting admin verification.');
    }
}

