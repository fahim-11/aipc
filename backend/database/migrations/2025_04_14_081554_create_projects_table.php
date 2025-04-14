<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Simple text fields for now, normalize later if needed
            $table->string('contractor_name');
            $table->string('contractor_contact')->nullable();
            $table->string('consultancy_name');
            $table->string('consultancy_contact')->nullable();
            // If using foreign keys:
            // $table->foreignId('contractor_id')->nullable()->constrained('contractors')->onDelete('set null');
            // $table->foreignId('consultancy_id')->nullable()->constrained('consultancies')->onDelete('set null');
            $table->string('location');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['Planning', 'Under Construction', 'Final Inspection', 'Completed'])->default('Planning');
            $table->text('phases_milestones_details')->nullable(); // Simple text for now
            $table->timestamps();
            
        });
    }
    public function down(): void { Schema::dropIfExists('projects'); }
};