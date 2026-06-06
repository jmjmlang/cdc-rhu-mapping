<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds account_status enum to users table.
     *
     * New citizen registrations default to 'pending' and must be approved
     * by an admin before they can log in. The admin account (seeded) is
     * set to 'approved' via the seeder. This mirrors the case_reports
     * approval pattern already established in the application.
     *
     * Values:
     *   pending  — registered, awaiting admin review
     *   approved — can log in and use the application
     *   rejected — registration denied by admin
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('account_status', ['pending', 'approved', 'rejected'])
                ->default('approved')  // existing users are auto-approved
                ->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('account_status');
        });
    }
};
