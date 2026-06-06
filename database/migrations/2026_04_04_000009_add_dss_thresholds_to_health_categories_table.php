<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds a nullable JSON column `dss_thresholds` to `health_categories`.
 *
 * Format: {"moderate": 5, "high": 15, "critical": 30}
 *
 * Null = use DssService fallback defaults (5, 15, 30).
 * Admin can edit per-category via the Decision Support page.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_categories', function (Blueprint $table) {
            $table->json('dss_thresholds')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('health_categories', function (Blueprint $table) {
            $table->dropColumn('dss_thresholds');
        });
    }
};
