<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Consultancy;
use Illuminate\Support\Facades\DB; // Import the DB facade

class ConsultancySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('consultancies')->truncate(); // Truncate the table before seeding

        // Create Consultancy records with specific IDs to match the React component
        Consultancy::create([
            'id' => 1, // Important: Match the ID in ProjectRegistration.jsx
            'consultancy_name' => 'Consultancy X',
            'phone_number' => '111-222-3333',
            'email_address' => 'consultancyX@example.com',
            'company_address' => '111 Pine St',
        ]);

        Consultancy::create([
            'id' => 2, // Important: Match the ID in ProjectRegistration.jsx
            'consultancy_name' => 'Consultancy Y',
            'phone_number' => '444-555-6666',
            'email_address' => 'consultancyY@example.com',
            'company_address' => '444 Maple Ave',
        ]);

        Consultancy::create([
            'id' => 3, // Important: Match the ID in ProjectRegistration.jsx
            'consultancy_name' => 'Consultancy Z',
            'phone_number' => '777-888-9999',
            'email_address' => 'consultancyZ@example.com',
            'company_address' => '777 Oak Ln',
        ]);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}