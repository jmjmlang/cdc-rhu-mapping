<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_reports', function (Blueprint $table) {
            $table->text('deletion_reason')->nullable()->after('reviewed_at');
            $table->softDeletes()->after('deletion_reason');
        });
    }

    public function down(): void
    {
        Schema::table('case_reports', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('deletion_reason');
        });
    }
};
