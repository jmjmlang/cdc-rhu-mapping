<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barangay extends Model
{
    protected $fillable = ['municipality_id', 'name', 'latitude', 'longitude'];

    protected $casts = [
        'latitude'  => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function caseReports(): HasMany
    {
        return $this->hasMany(CaseReport::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
