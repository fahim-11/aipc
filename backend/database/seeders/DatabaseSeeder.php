<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user. Make sure you have the 'name' column in your users table!
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Call other seeders (if you have them).  Important: Move this *outside* the User::factory()->create() call.
        $this->call([
            ConsultancySeeder::class,
            ContractorSeeder::class,
            UserSeeder::class, //If UserSeeder also attempts to use the name column, make sure you created it using the migration.
            // Add other seeders here
        ]);
    }
}