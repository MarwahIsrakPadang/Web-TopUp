<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@topup.test',
            'password' => bcrypt('password'),
            'role' => UserRoleEnum::Admin,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Customer',
            'email' => 'customer@topup.test',
            'password' => bcrypt('password'),
            'role' => UserRoleEnum::Customer,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Akun admin dan customer berhasil dibuat.');
    }
}
