<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermission::class,
        ]);

        $superAdmin = User::create([
            'email' => 'test@example.com',
            'password' => Hash::make('test@example.com'),
        ]);

        $superAdmin->assignRole('super_admin');

        $superManager = User::create([
            'email' => 'manager@example.com',
            'password' => Hash::make('manager@example.com'),
        ]);

        $superManager->assignRole('manager');

        $superEmploye = User::create([
            'email' => 'employe@example.com',
            'password' => Hash::make('employe@example.com'),
        ]);

        $superEmploye->assignRole('employe');
    }
}
