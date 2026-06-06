<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseReportAction extends Model
{
    protected $fillable = [
        'case_report_id',
        'user_id',
        'action_type',
        'priority',
        'audience',
        'status',
        'message',
        'due_date',
        'completed_at',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    public function caseReport(): BelongsTo
    {
        return $this->belongsTo(CaseReport::class)->withTrashed();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    public function scopeCitizenVisible(Builder $query): Builder
    {
        return $query->whereIn('audience', ['citizen_visible', 'affected_citizens', 'all_users']);
    }

    public function scopeAffectedCitizenVisible(Builder $query): Builder
    {
        return $query->whereIn('audience', ['citizen_visible', 'affected_citizens']);
    }

    public function scopeAllUsers(Builder $query): Builder
    {
        return $query->where('audience', 'all_users');
    }
}
