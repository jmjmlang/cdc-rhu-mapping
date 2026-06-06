<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'org_role',
        'account_status',
        'barangay_id',
        'gender',
        'birthdate',
        'age',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'birthdate'         => 'date',
        ];
    }

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    public function caseReports(): HasMany
    {
        return $this->hasMany(CaseReport::class);
    }

    public function reviewedReports(): HasMany
    {
        return $this->hasMany(CaseReport::class, 'reviewed_by');
    }

    public function caseReportActions(): HasMany
    {
        return $this->hasMany(CaseReportAction::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCitizen(): bool
    {
        return $this->role === 'citizen';
    }

    public function isRhu(): bool
    {
        return $this->role === 'rhu';
    }

    public function isPending(): bool
    {
        return $this->account_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->account_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->account_status === 'rejected';
    }
}
