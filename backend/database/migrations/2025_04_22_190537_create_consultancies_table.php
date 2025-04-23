// AMHARA-IP-PROJECT/backend/database/migrations/xxxx_xx_xx_create_consultancies_table.php

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
        Schema::create('consultancies', function (Blueprint $table) {
            $table->id();
            $table->string('consultancy_name');
            $table->string('phone_number');
            $table->string('email_address');
            $table->string('company_address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultancies');
    }
};