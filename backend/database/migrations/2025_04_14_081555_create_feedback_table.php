<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('project_name')->nullable(); // Added project_name
            $table->string('complaint_type');
            $table->text('description');
            $table->string('contact_info')->nullable();
            $table->enum('status', ['New', 'In Progress', 'Resolved'])->default('New');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('feedback');
    }
};