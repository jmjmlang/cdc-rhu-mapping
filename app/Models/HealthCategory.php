<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HealthCategory extends Model
{
    protected $fillable = ['name', 'description', 'prevention_tips', 'action_steps', 'quiz_data', 'dss_thresholds'];

    protected function casts(): array
    {
        return [
            'prevention_tips' => 'array',
            'action_steps'    => 'array',
            'quiz_data'       => 'array',
            'dss_thresholds'  => 'array',
        ];
    }

    public function caseReports(): HasMany
    {
        return $this->hasMany(CaseReport::class);
    }
}
