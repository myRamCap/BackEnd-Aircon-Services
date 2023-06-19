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
            'first_name' => 'Jhon Ray',
            'middle_name' => 'Lorenzo',
            'last_name' => 'Angcon',
            'role_id' => '1',
            'email' => 'jhonrayangcon1423@gmail.com',
            'password' => bcrypt('welcome@123'),
            'contact_number' => '123456789',
            'image' => 'image_test'
        ]);

        \App\Models\Client::insert([
            [ 
                'first_name' => 'Jhon', 
                'last_name' => 'Ray', 
                'email' => 'jhonray@email.com', 
                'contact_number' => '1235456', 
                'address' => 'lalam akasya', 
                'is_activated' => '1', 
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
                'remember_token' => null, 
                'created_at' => now(), 
                'updated_at' => now(), 
            ],
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
                'name' => 'Branch Advisor', 
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
