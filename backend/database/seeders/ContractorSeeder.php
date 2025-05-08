<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contractor;
use Illuminate\Support\Facades\DB; // Import the DB facade

class ContractorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('contractors')->truncate(); // Truncate the table before seeding

        Contractor::create([
            'id' => 1,  //Important, The IDS should match the ones in react
            'contractor_name' => 'Contractor A',
            'phone_number' => '123-456-7890',
            'email_address' => 'contractorA@example.com',
            'company_address' => '123 Main St',
            'unique_id' => 'CON-001'
        ]);
        Contractor::create([
             'id' => 2, //Important, The IDS should match the ones in react
            'contractor_name' => 'Contractor B',
            'phone_number' => '456-789-0123',
            'email_address' => 'contractorB@example.com',
            'company_address' => '456 Elm St',
            'unique_id' => 'CON-002'
        ]);
        Contractor::create([
             'id' => 3,  //Important, The IDS should match the ones in react
            'contractor_name' => 'Contractor C',
            'phone_number' => '789-012-3456',
            'email_address' => 'contractorC@example.com',
            'company_address' => '789 Oak St',
            'unique_id' => 'CON-003'
        ]);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}