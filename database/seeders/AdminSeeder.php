<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@wira242.com'],
            [
                'name'     => 'Admin Wira 242',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        $this->command->info('Admin account created: admin@wira242.com / admin123');
    }
}
