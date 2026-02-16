<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'first_name' => 'admin',
            'last_name' => 'administrator',
            'phone' => '123456789',
            'role_id' => 1,
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'email_verified' => true,
            'phone_verified' => true,
            'user_verified' => true,
            'status'=>"active",
        ]);
    }
}
