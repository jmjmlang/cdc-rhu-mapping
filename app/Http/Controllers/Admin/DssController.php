<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\DssService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DssController extends Controller
{
    public function __construct(private readonly DssService $dss) {}

    public function index()
    {
        $flat    = $this->dss->analyse();
        $grouped = $this->dss->groupByBarangay($flat);
        $summary = $this->dss->summary($flat);

        // Chart data
        $barangayNames = $grouped->pluck('barangay')->values()->all();
        $diseaseNames  = $flat->pluck('health_category')->unique()->values()->all();

        $palette = [
            'rgba(13,148,136,0.75)', 'rgba(245,158,11,0.75)', 'rgba(239,68,68,0.75)',
            'rgba(59,130,246,0.75)', 'rgba(168,85,247,0.75)', 'rgba(34,197,94,0.75)',
            'rgba(249,115,22,0.75)', 'rgba(236,72,153,0.75)', 'rgba(107,114,128,0.75)',
            'rgba(14,165,233,0.75)',
        ];

        // Stacked bar: cases per barangay by disease
        $chartByBarangay = [];
        foreach ($diseaseNames as $i => $disease) {
            $data = [];
            foreach ($barangayNames as $brgy) {
                $match = $flat->first(fn ($r) => $r['barangay'] === $brgy && $r['health_category'] === $disease);
                $data[] = $match ? $match['total_cases'] : 0;
            }
            $chartByBarangay[] = [
                'label'           => $disease,
                'data'            => $data,
                'backgroundColor' => $palette[$i % count($palette)],
            ];
        }

        // Risk distribution doughnut
        $riskLabels = ['Low', 'Moderate', 'High', 'Critical'];
        $riskColors = [
            'rgba(107,114,128,0.75)', 'rgba(245,158,11,0.75)',
            'rgba(249,115,22,0.75)', 'rgba(239,68,68,0.75)',
        ];
        $riskCounts = [];
        foreach ($riskLabels as $level) {
            $riskCounts[] = $flat->where('risk_level', $level)->count();
        }

        $thresholds = [
            'moderate' => (int) Setting::get('dss_threshold_moderate', 5),
            'high'     => (int) Setting::get('dss_threshold_high', 15),
            'critical' => (int) Setting::get('dss_threshold_critical', 30),
        ];

        return view('pages.admin.dss.index', compact(
            'grouped', 'summary',
            'barangayNames', 'chartByBarangay',
            'riskLabels', 'riskCounts', 'riskColors',
            'thresholds',
        ));
    }

    public function updateThresholds(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'moderate' => ['required', 'integer', 'min:1', 'max:9999'],
            'high'     => ['required', 'integer', 'min:1', 'max:9999'],
            'critical' => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        Setting::set('dss_threshold_moderate', $validated['moderate']);
        Setting::set('dss_threshold_high',     $validated['high']);
        Setting::set('dss_threshold_critical', $validated['critical']);

        return back()->with('success', 'Risk thresholds updated.');
    }
}
