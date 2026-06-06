<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Alters the users.role enum to include 'rhu' alongside 'admin' and 'citizen'.
     * MySQL ENUM alteration is an in-place schema change; existing rows are unaffected.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'citizen', 'rhu') NOT NULL DEFAULT 'citizen'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * Drops 'rhu' back out. Any existing rhu users will fail if their role
     * is still 'rhu' — ensure they are reassigned before rolling back.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'citizen') NOT NULL DEFAULT 'citizen'");
        }
    }
};
