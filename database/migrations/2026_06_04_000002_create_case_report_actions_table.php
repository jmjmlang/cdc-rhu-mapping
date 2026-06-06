<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('case_report_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('action_type', [
                'field_visit',
                'health_education',
                'referral',
                'follow_up',
                'supply_request',
                'case_validation',
            ])->default('follow_up');
            $table->enum('priority', ['routine', 'urgent'])->default('routine');
            $table->enum('audience', ['admin_only', 'citizen_visible'])->default('admin_only');
            $table->enum('status', ['open', 'completed'])->default('open');
            $table->text('message');
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['audience', 'status']);
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('case_report_actions');
        Schema::enableForeignKeyConstraints();
    }
};
