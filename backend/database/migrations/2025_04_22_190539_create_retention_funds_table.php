// AMHARA-IP-PROJECT/backend/database/migrations/xxxx_xx_xx_create_retention_funds_table.php

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
        Schema::create('retention_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->decimal('amount_held', 15, 2);
            $table->text('conditions_for_release');
            $table->boolean('actual_release_status')->default(false);
            $table->date('withheld_date');
            $table->date('released_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retention_funds');
    }
};