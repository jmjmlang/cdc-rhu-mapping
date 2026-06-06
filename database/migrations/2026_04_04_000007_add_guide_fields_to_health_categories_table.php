<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_categories', function (Blueprint $table) {
            $table->json('prevention_tips')->nullable()->after('description');
            $table->json('action_steps')->nullable()->after('prevention_tips');
            $table->json('quiz_data')->nullable()->after('action_steps');
        });
    }

    public function down(): void
    {
        Schema::table('health_categories', function (Blueprint $table) {
            $table->dropColumn(['prevention_tips', 'action_steps', 'quiz_data']);
        });
    }
};
