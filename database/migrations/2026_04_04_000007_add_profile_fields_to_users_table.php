<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds citizen profile fields: gender, birthdate, age.
     * Age is stored as a computed convenience — birthdate is the source of truth.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('users', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female'])->nullable()->after('name');
            $table->date('birthdate')->nullable()->after('gender');
            $table->unsignedTinyInteger('age')->nullable()->after('birthdate');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gender', 'birthdate', 'age']);
        });

        Schema::enableForeignKeyConstraints();
    }
};
