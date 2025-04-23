<?php

// AMHARA-IP-PROJECT/backend/database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Change 'password' to a secure password
            'role' => 'admin',
        ]);

        // Create an official user
        User::create([
            'name' => 'Official User',
            'email' => 'official@example.com',
            'password' => Hash::make('password'), // Change 'password' to a secure password
            'role' => 'official',
        ]);

        // Create a contractor user (assuming you have a contractor in your database)
        $contractor = \App\Models\Contractor::first(); // Get the first contractor

        if ($contractor) {
            User::create([
                'name' => 'Contractor User',
                'email' => 'contractor@example.com',
                'password' => Hash::make('password'), // Change 'password' to a secure password
                'role' => 'contractor',
                'contractor_id' => $contractor->id,
            ]);
        }

        // Create a public user
        User::create([
            'name' => 'Public User',
            'email' => 'public@example.com',
            'password' => Hash::make('password'), // Change 'password' to a secure password
            'role' => 'public',
        ]);
    }
}