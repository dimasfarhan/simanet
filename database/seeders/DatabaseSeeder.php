<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\BoardOfDirector;
use App\Models\GeneralAssistant;
use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $staffRole = Role::create([
            'name' => 'staff',
        ]);
        $gaRole = Role::create([
            'name' => 'ga',
        ]);
        $bodRole = Role::create([
            'name' => 'bod',
        ]);

        $user1 = User::create([
            'username' => 'bodOke',
            'email' => 'bod@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::create([
            'username' => 'gaOke',
            'email' => 'ga@example.com',
            'password' => bcrypt('password'),
        ]);

        $user3 = User::create([
            'username' => 'staffOke',
            'email' => 'staff@example.com',
            'password' => bcrypt('password'),
        ]);

        $user1->roles()->attach($bodRole);
        $user1->save();
        $user2->roles()->attach($gaRole);
        $user2->save();
        $user3->roles()->attach($staffRole);
        $user3->save();

        $bod = BoardOfDirector::create([
            'user_uuid' => $user1->uuid,
            'uuid' => Uuid::uuid4()->toString(),
            'name' => 'John Doe',
            'nip' => '1234567890',
            'phone' => '081234567890',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '1990-01-01',
            'gender' => 'Male',
            'religion' => 'Islam',
            'address' => 'Jl. Sudirman No. 123, Jakarta',
            'is_active' => true,
            'date_joined' => '2021-01-01',
        ]);

        $ga = GeneralAssistant::create([
            'user_uuid' => $user2->uuid,
            'uuid' => Uuid::uuid4()->toString(),
            'name' => 'Jane Doe',
            'nip' => '0987654321',
            'phone' => '081382148128',
            'place_of_birth' => 'Surabaya',
            'date_of_birth' => '1992-05-20',
            'gender' => 'Female',
            'religion' => 'Kristen',
            'address' => 'Jl. Thamrin No. 456, Surabaya',
            'is_active' => true,
            'date_joined' => '2021-01-01',
        ]);

        $staff = Staff::create([
            'user_uuid' => $user3->uuid,
            'uuid' => Uuid::uuid4()->toString(),
            'name' => 'John Doe',
            'nip' => '12345',
            'phone' => '082184729554',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '1990-01-01',
            'gender' => 'MALE',
            'religion' => 'Islam',
            'address' => 'Jalan Raya No. 1',
            'is_active' => true,
            'date_joined' => '2020-01-01',
        ]);
    }
}
