<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('retention_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->decimal('amount_held', 15, 2);
            $table->text('release_conditions')->nullable();
            $table->enum('status', ['Held', 'Released'])->default('Held');
            $table->date('release_date')->nullable();
            $table->timestamps();
            $table->unique('project_id'); // Assuming one retention record per project
        });
    }
    public function down(): void { Schema::dropIfExists('retention_funds'); }
};