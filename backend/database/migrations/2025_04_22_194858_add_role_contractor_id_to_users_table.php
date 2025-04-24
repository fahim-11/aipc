<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove the line that adds the 'role' column
            // $table->string('role')->default('public'); // This line should be removed
            $table->foreignId('contractor_id')->nullable()->constrained('contractors')->onDelete('set null'); // Add contractor_id column, nullable
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop contractor_id first to avoid foreign key constraint issues
            $table->dropForeign(['contractor_id']);
            $table->dropColumn('contractor_id');
        });
    }
};