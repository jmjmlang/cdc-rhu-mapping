<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('case_reports', function (Blueprint $table) {
            $table->string('patient_name', 191)->nullable()->after('notes');
            $table->unsignedTinyInteger('patient_age')->nullable()->after('patient_name');
            $table->enum('patient_gender', ['male', 'female'])->nullable()->after('patient_age');
            $table->date('patient_birthdate')->nullable()->after('patient_gender');
            $table->text('symptoms')->nullable()->after('patient_birthdate');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
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
};
