<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HealthCategoryController extends Controller
{
    public function index(): View
    {
        $categories = HealthCategory::orderBy('name')->withCount('caseReports')->get();

        return view('pages.admin.health-categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:191', 'unique:health_categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        HealthCategory::create($validated);

        return back()->with('success', "Health category \"{$validated['name']}\" added.");
    }

    public function update(Request $request, HealthCategory $healthCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:191', "unique:health_categories,name,{$healthCategory->id}"],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $healthCategory->update($validated);

        return back()->with('success', "Health category updated.");
    }

    public function updateGuide(Request $request, HealthCategory $healthCategory): RedirectResponse
    {
        $preventionRaw = $request->input('prevention_tips', '');
        $actionRaw     = $request->input('action_steps', '');

        $preventionTips = array_values(array_filter(array_map('trim', explode("\n", $preventionRaw))));
        $actionSteps    = array_values(array_filter(array_map('trim', explode("\n", $actionRaw))));

        $healthCategory->update([
            'prevention_tips' => $preventionTips ?: null,
            'action_steps'    => $actionSteps ?: null,
        ]);

        return back()->with('success', "Health guide for \"{$healthCategory->name}\" updated.");
    }

    public function destroy(HealthCategory $healthCategory): RedirectResponse
    {
        if ($healthCategory->caseReports()->count() > 0) {
            return back()->with('error', "Cannot delete \"{$healthCategory->name}\" — it has linked case reports.");
        }

        $healthCategory->delete();

        return back()->with('success', "Health category \"{$healthCategory->name}\" deleted.");
    }
}
