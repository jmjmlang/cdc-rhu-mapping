<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Removes patient-detail columns from case_reports.
     *
     * Case reports track aggregated case *counts* per barangay/category —
     * not individual patient records. Attaching a single patient name to a
     * report of (e.g.) 12 dengue cases is logically incorrect and was
     * confusing in the UI. These fields are being dropped.
     *
     * Symptoms were also per-individual; they don't map to a batch count.
     * The `notes` column (free-text on the report, not a patient) remains.
     */
    public function up(): void
    {
        Schema::table('case_reports', function (Blueprint $table) {
            $table->dropColumn([
                'patient_name',
                'patient_age',
                'patient_gender',
                'patient_birthdate',
                'symptoms',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('case_reports', function (Blueprint $table) {
            $table->string('patient_name', 191)->nullable()->after('notes');
            $table->unsignedTinyInteger('patient_age')->nullable()->after('patient_name');
            $table->enum('patient_gender', ['male', 'female'])->nullable()->after('patient_age');
            $table->date('patient_birthdate')->nullable()->after('patient_gender');
            $table->text('symptoms')->nullable()->after('patient_birthdate');
        });
    }
};
