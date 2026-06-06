<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('case_reports', function (Blueprint $table) {
            $table->id();

            // Who submitted — nullOnDelete so reports survive user account deletion
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Where — restrictOnDelete so a barangay with reports cannot be accidentally deleted
            $table->foreignId('barangay_id')->constrained()->restrictOnDelete();

            // What disease — restrictOnDelete to protect historical data integrity
            $table->foreignId('health_category_id')->constrained('health_categories')->restrictOnDelete();

            $table->unsignedSmallInteger('number_of_cases');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->date('report_date');
            $table->text('notes')->nullable();

            // Admin review audit trail — nullOnDelete so records survive if reviewer's account is deleted
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('case_reports');
        Schema::enableForeignKeyConstraints();
    }
};
