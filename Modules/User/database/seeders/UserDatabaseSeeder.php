<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;

class UserDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Test User',
                'email' => 'test@gmail.com',
                'password' => Hash::make('12345678'),
            ],
            [
                'name' => 'Test User 2',
                'email' => 'test+2@gmail.com',
                'password' => Hash::make('12345678'),
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }
    }
}
