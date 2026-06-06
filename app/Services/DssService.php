<?php

namespace App\Services;

use App\Models\CaseReport;
use App\Models\Setting;
use Illuminate\Support\Collection;

class DssService
{
    private function thresholds(): array
    {
        return [
            'moderate' => (int) Setting::get('dss_threshold_moderate', 5),
            'high'     => (int) Setting::get('dss_threshold_high', 15),
            'critical' => (int) Setting::get('dss_threshold_critical', 30),
        ];
    }

    public function analyse(): Collection
    {
        $reports = CaseReport::approved()
            ->withinDays(30)
            ->with(['barangay', 'healthCategory'])
            ->get()
            ->groupBy(fn ($r) => $r->barangay_id . '_' . $r->health_category_id);

        return $reports->map(function ($group) {
            $first      = $group->first();
            $totalCases = $group->sum('number_of_cases');
            $riskLevel  = $this->evaluateRisk($totalCases);

            return [
                'barangay'        => $first->barangay->name,
                'health_category' => $first->healthCategory->name,
                'total_cases'     => $totalCases,
                'risk_level'      => $riskLevel,
                'tasks'           => $this->getRecommendations($riskLevel, $first->barangay->name, $first->healthCategory->name, $totalCases),
            ];
        })->values()->sortByDesc('total_cases')->values();
    }

    public function groupByBarangay(Collection $flat): Collection
    {
        $riskOrder = ['Low' => 0, 'Moderate' => 1, 'High' => 2, 'Critical' => 3];

        return $flat
            ->groupBy('barangay')
            ->map(function ($diseases, $barangay) use ($riskOrder) {
                $worstRisk = $diseases
                    ->sortByDesc(fn ($r) => $riskOrder[$r['risk_level']])
                    ->first()['risk_level'];

                return [
                    'barangay'    => $barangay,
                    'worst_risk'  => $worstRisk,
                    'total_cases' => $diseases->sum('total_cases'),
                    'diseases'    => $diseases->sortByDesc('total_cases')->values(),
                ];
            })
            ->sortByDesc(fn ($b) => [$riskOrder[$b['worst_risk']], $b['total_cases']])
            ->values();
    }

    public function summary(Collection $flat): array
    {
        if ($flat->isEmpty()) {
            return [
                'total_cases'        => 0,
                'affected_barangays' => 0,
                'critical_count'     => 0,
                'high_count'         => 0,
            ];
        }

        return [
            'total_cases'        => $flat->sum('total_cases'),
            'affected_barangays' => $flat->pluck('barangay')->unique()->count(),
            'critical_count'     => $flat->where('risk_level', 'Critical')->count(),
            'high_count'         => $flat->where('risk_level', 'High')->count(),
        ];
    }

    private function evaluateRisk(int $cases): string
    {
        $t = $this->thresholds();

        return match (true) {
            $cases >= $t['critical'] => 'Critical',
            $cases >= $t['high']     => 'High',
            $cases >= $t['moderate'] => 'Moderate',
            default                  => 'Low',
        };
    }

    private function getRecommendations(string $risk, string $barangay, string $category, int $cases): array
    {
        $base = "{$cases} {$category} case(s) reported in {$barangay}.";

        return match ($risk) {
            'Critical' => [
                $base . ' Immediate action required.',
                'Deploy health response team to the barangay.',
                'Alert the Municipal Health Office immediately.',
                'Set up temporary health station if needed.',
                'Conduct community-wide health information drive.',
            ],
            'High' => [
                $base . ' Elevated risk — close monitoring needed.',
                'Increase health worker visits to the area.',
                'Prepare medical supplies and coordinate with MHO.',
                'Conduct health awareness sessions in the barangay.',
            ],
            'Moderate' => [
                $base . ' Monitor situation and take preventive measures.',
                'Schedule community health education sessions.',
                'Ensure adequate medical supplies are available.',
                'Follow up on reported cases.',
            ],
            default => [
                $base . ' Risk level is low — continue routine monitoring.',
                'Maintain regular health surveillance.',
                'Continue health education programs.',
            ],
        };
    }
}
