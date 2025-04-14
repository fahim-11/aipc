<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inspection_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->date('inspection_date');
            $table->string('inspector_name');
            $table->text('findings');
            $table->string('report_file_path')->nullable(); // Path to uploaded file
            $table->string('original_file_name')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('inspection_reports'); }
};