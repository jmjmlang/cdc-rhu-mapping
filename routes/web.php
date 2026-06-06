<?php

use App\Http\Controllers\Admin\CaseReportVerificationController;
use App\Http\Controllers\Admin\DssController;
use App\Http\Controllers\Admin\UserApprovalController;
use App\Http\Controllers\Admin\HealthCategoryController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\CaseReportActionController;
use App\Http\Controllers\Citizen\CaseReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RHU\DashboardController as RhuDashboardController;
use App\Http\Controllers\RHU\ReportVerificationController as RhuReportVerificationController;
use Illuminate\Support\Facades\Route;

// ── Landing — redirect to login ────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));

// ── Registration pending page (guest-accessible after signup) ──
Route::get('/register/pending', fn () => view('pages.auth.register-pending'))->name('register.pending');

// ── Authenticated (shared) ────────────────────────────────────
Route::middleware(['auth', 'prevent-back-history'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/map', [MapController::class, 'index'])->name('map.index');
    Route::get('/api/map-data', [MapController::class, 'data'])->name('api.map-data');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});

// ── Citizen actions ───────────────────────────────────────────
Route::middleware(['auth', 'role:citizen', 'prevent-back-history'])->prefix('citizen')->name('citizen.')->group(function () {
    Route::post('/reports', [CaseReportController::class, 'store'])->name('reports.store');
    Route::get('/health-guide', [DashboardController::class, 'healthGuide'])->name('health-guide');
});

// ── Admin actions ─────────────────────────────────────────────
Route::middleware(['auth', 'role:admin', 'prevent-back-history'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/reports',                    [AdminReportController::class, 'index'])->name('reports.index');
    Route::post('/reports',                   [CaseReportVerificationController::class, 'store'])->name('reports.store');
    Route::patch('/reports/{report}/approve', [CaseReportVerificationController::class, 'approve'])->name('reports.approve');
    Route::patch('/reports/{report}/reject',  [CaseReportVerificationController::class, 'reject'])->name('reports.reject');
    Route::patch('/reports/{report}',         [CaseReportVerificationController::class, 'update'])->name('reports.update');
    Route::delete('/reports/{report}',        [CaseReportVerificationController::class, 'destroy'])->name('reports.destroy');

    Route::get('/health-categories',                              [HealthCategoryController::class, 'index'])->name('health-categories.index');
    Route::post('/health-categories',                             [HealthCategoryController::class, 'store'])->name('health-categories.store');
    Route::patch('/health-categories/{healthCategory}',           [HealthCategoryController::class, 'update'])->name('health-categories.update');
    Route::patch('/health-categories/{healthCategory}/guide',     [HealthCategoryController::class, 'updateGuide'])->name('health-categories.update-guide');
    Route::delete('/health-categories/{healthCategory}',          [HealthCategoryController::class, 'destroy'])->name('health-categories.destroy');

    Route::get('/dss',              [DssController::class, 'index'])->name('dss');
    Route::post('/dss/thresholds', [DssController::class, 'updateThresholds'])->name('dss.thresholds');

    Route::get('/users',                    [UserApprovalController::class, 'index'])->name('users.index');
    Route::post('/users/rhu',               [UserApprovalController::class, 'storeRhu'])->name('users.rhu.store');
    Route::patch('/users/{user}/approve',   [UserApprovalController::class, 'approve'])->name('users.approve');
    Route::patch('/users/{user}/reject',    [UserApprovalController::class, 'reject'])->name('users.reject');
    Route::patch('/users/{user}',           [UserApprovalController::class, 'update'])->name('users.update');

    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

    Route::get('/case-coordination', [CaseReportActionController::class, 'index'])->name('case-actions.index');
    Route::post('/case-coordination', [CaseReportActionController::class, 'store'])->name('case-actions.store');
    Route::patch('/case-coordination/{caseAction}/complete', [CaseReportActionController::class, 'complete'])->name('case-actions.complete');
});

// ── RHU (Rural Health Unit) actions ──────────────────────────────
Route::middleware(['auth', 'role:rhu', 'prevent-back-history'])->prefix('rhu')->name('rhu.')->group(function () {
    Route::get('/dashboard', [RhuDashboardController::class, 'index'])->name('dashboard');
    Route::patch('/reports/{report}/approve', [RhuReportVerificationController::class, 'approve'])->name('reports.approve');
    Route::patch('/reports/{report}/reject',  [RhuReportVerificationController::class, 'reject'])->name('reports.reject');
    Route::get('/case-coordination', [CaseReportActionController::class, 'index'])->name('case-actions.index');
    Route::post('/case-coordination', [CaseReportActionController::class, 'store'])->name('case-actions.store');
    Route::patch('/case-coordination/{caseAction}/complete', [CaseReportActionController::class, 'complete'])->name('case-actions.complete');
});

require __DIR__.'/auth.php';
