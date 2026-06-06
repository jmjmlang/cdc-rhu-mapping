<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE case_report_actions
            MODIFY action_type ENUM(
                'field_visit',
                'health_education',
                'referral',
                'follow_up',
                'supply_request',
                'case_validation',
                'public_advisory'
            ) NOT NULL DEFAULT 'follow_up'
        ");

        DB::statement("
            ALTER TABLE case_report_actions
            MODIFY audience ENUM(
                'admin_only',
                'citizen_visible',
                'affected_citizens',
                'all_users'
            ) NOT NULL DEFAULT 'admin_only'
        ");
    }

    public function down(): void
    {
        DB::table('case_report_actions')
            ->where('action_type', 'public_advisory')
            ->update(['action_type' => 'health_education']);

        DB::table('case_report_actions')
            ->where('audience', 'affected_citizens')
            ->update(['audience' => 'citizen_visible']);

        DB::table('case_report_actions')
            ->where('audience', 'all_users')
            ->update(['audience' => 'admin_only']);

        DB::statement("
            ALTER TABLE case_report_actions
            MODIFY action_type ENUM(
                'field_visit',
                'health_education',
                'referral',
                'follow_up',
                'supply_request',
                'case_validation'
            ) NOT NULL DEFAULT 'follow_up'
        ");

        DB::statement("
            ALTER TABLE case_report_actions
            MODIFY audience ENUM(
                'admin_only',
                'citizen_visible'
            ) NOT NULL DEFAULT 'admin_only'
        ");
    }
};
