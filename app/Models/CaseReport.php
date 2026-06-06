<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'barangay_id',
        'health_category_id',
        'number_of_cases',
        'status',
        'report_date',
        'notes',
        'reviewed_by',
        'reviewed_at',
        'deletion_reason',
    ];

    protected $casts = [
        'report_date'  => 'date',
        'reviewed_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    public function healthCategory(): BelongsTo
    {
        return $this->belongsTo(HealthCategory::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(CaseReportAction::class);
    }

    public function citizenVisibleActions(): HasMany
    {
        return $this->actions()->whereIn('audience', ['citizen_visible', 'affected_citizens', 'all_users'])->latest();
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function scopeWithinDays(Builder $query, int $days = 30): Builder
    {
        return $query->where('report_date', '>=', now()->subDays($days)->toDateString());
    }
}
