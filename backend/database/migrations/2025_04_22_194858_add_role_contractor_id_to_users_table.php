// AMHARA-IP-PROJECT/backend/database/migrations/xxxx_xx_xx_add_role_contractor_id_to_users_table.php

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
            $table->string('role')->default('public'); // Add role column with default value 'public'
            $table->foreignId('contractor_id')->nullable()->constrained('contractors')->onDelete('set null'); // Add contractor_id column, nullable
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropForeign(['contractor_id']);
            $table->dropColumn('contractor_id');
        });
    }
};