<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds home barangay association to users.
     *
     * Citizens can optionally be linked to their home barangay. This
     * pre-fills the barangay field on submission forms and enables
     * barangay-scoped notification subscriptions in future features.
     *
     * Runs AFTER create_barangays_table so the FK is safe to define.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('barangay_id')
                ->nullable()
                ->after('role')
                ->constrained()
                ->nullOnDelete();
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropColumn('barangay_id');
        });

        Schema::enableForeignKeyConstraints();
    }
};
