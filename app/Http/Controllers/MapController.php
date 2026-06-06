<?php

namespace App\Http\Controllers;

use App\Models\CaseReport;
use App\Models\HealthCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class MapController extends Controller
{
    public function index(): View
    {
        $categories = HealthCategory::orderBy('name')->get();

        return view('pages.map.index', compact('categories'));
    }

    public function data(): JsonResponse
    {
        $data = CaseReport::approved()
            ->withinDays(30)
            ->with(['barangay', 'healthCategory'])
            ->get()
            ->groupBy(fn ($r) => $r->barangay_id . '-' . $r->health_category_id)
            ->map(function ($reports) {
                $first = $reports->first();
                return [
                    'barangay'    => $first->barangay->name,
                    'latitude'    => (float) $first->barangay->latitude,
                    'longitude'   => (float) $first->barangay->longitude,
                    'category_id' => $first->health_category_id,
                    'category'    => $first->healthCategory->name,
                    'total_cases' => $reports->sum('number_of_cases'),
                ];
            })
            ->values();

        return response()->json($data);
    }
}
