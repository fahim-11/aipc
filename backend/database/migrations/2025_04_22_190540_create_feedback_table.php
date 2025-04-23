// AMHARA-IP-PROJECT/backend/database/migrations/xxxx_xx_xx_create_feedback_table.php

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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_type');
            $table->text('description');
            $table->string('contact_email')->nullable();
            $table->enum('status', ['new', 'in progress', 'resolved', 'closed'])->default('new'); // Added status field
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};