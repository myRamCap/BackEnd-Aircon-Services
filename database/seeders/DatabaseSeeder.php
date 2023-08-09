<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::create([
            [
                'first_name' => 'Super',
                'middle_name' => '',
                'last_name' => 'Admin',
                'role_id' => '1',
                'is_activated' => '1',
                'status' => 'active',
                'email' => 'admin@mangpogs.com',
                'password' => bcrypt('welcome@123'),
                'contact_number' => '123456789',
                'image' => 'image_test',
                'created_at' => now(), 
                'updated_at' => now(), 
            ],
            [
                'first_name' => 'Demo',
                'middle_name' => '',
                'last_name' => 'Demo',
                'role_id' => '1',
                'is_activated' => '1',
                'status' => 'active',
                'email' => 'demo@mangpogs.com',
                'password' => bcrypt('demo@123'),
                'contact_number' => '987654321',
                'image' => 'image_test',
                'created_at' => now(), 
                'updated_at' => now(), 
            ]
        ]);

        \App\Models\Client::insert([
            [ 
                'first_name' => 'Jhon', 
                'last_name' => 'Ray', 
                'email' => 'jhonray@email.com', 
                'contact_number' => '1235456', 
                'address' => 'lalam akasya', 
                'is_activated' => '1', 
                'active' => '1', 
                'remember_token' => null, 
                'created_at' => now(), 
                'updated_at' => now(), 
            ],
            [ 
                'first_name' => 'Randy', 
                'last_name' => 'Organ', 
                'email' => 'randy@email.com', 
                'contact_number' => '12354256', 
                'address' => 'lalam kwayan', 
                'is_activated' => '1', 
                'active' => '1',
                'remember_token' => null, 
                'created_at' => now(), 
                'updated_at' => now(), 
            ],
            [ 
                'first_name' => 'Default', 
                'last_name' => 'Default', 
                'email' => 'Default@email.com', 
                'contact_number' => '09123456789', 
                'address' => 'Default', 
                'is_activated' => '1', 
                'active' => '1',
                'remember_token' => null, 
                'created_at' => now(), 
                'updated_at' => now(), 
            ],
        ]);

        \App\Models\ClientToken::create([
            'token' => '1346',
            'contact_number' => '09123456789',
            'is_activated' => '0',
            'is_expired' => '0',
        ]);

        \App\Models\Verifytoken::create([
            'token' => '134679',
            'email' => 'demo@mangpogs.com',
            'is_activated' => '0',
            'is_expired' => '0',
        ]);

        \App\Models\Role::insert([
            // [ 'name' => 'Super Admin', ['access' => '1', 'access' => '2'], 'created_at' => now(), 'updated_at' => now(), ],
            // [ 'name' => 'Corporate Manager', 'created_at' => now(), 'updated_at' => now(), ],
            // [ 'name' => 'Branch Manager', 'created_at' => now(), 'updated_at' => now(), ],
            // [ 'name' => 'Branch Advisor', 'created_at' => now(), 'updated_at' => now(), ],
            [ 
                'name' => 'Super Admin', 
                'role_access' => '1,2,3,4', 
                'created_at' => now(), 
                'updated_at' => now(), 
            ],
            [ 
                'name' => 'Corporate Manager', 
                'role_access' => '3,4',
                'created_at' => now(), 
                'updated_at' => now(), 
            ],
            [ 
                'name' => 'Branch Manager', 
                'role_access' => '4',
                'created_at' => now(), 
                'updated_at' => now(), 
            ],
            [ 
                'name' => 'Technician', 
                'role_access' => null,
                'created_at' => now(), 
                'updated_at' => now(), 
            ],
        ]);

        $startTime = strtotime('00:00');
        $endTime = strtotime('23:30');
        $interval = 30 * 60; // 30 minutes in seconds

        $timeSlots = [];
        $currentTimestamp = $startTime;

        while ($currentTimestamp <= $endTime) {
            $time = date('H:i', $currentTimestamp);

            $timeSlots[] = [
                'time' => $time,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $currentTimestamp += $interval;
        }

        \App\Models\Time::insert($timeSlots);

        // \App\Models\User::factory(10)->create();

        // \App\Models\Service::factory(10)->create();

 
    }
}
