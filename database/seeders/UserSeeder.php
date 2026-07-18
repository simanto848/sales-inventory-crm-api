<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'kpi_score' => 0,
            ]
        );

        // Manager user
        User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password123'),
                'role' => 'manager',
                'kpi_score' => 0,
            ]
        );

        // Employee users
        $employees = [
            ['name' => 'Alice Williams', 'email' => 'alice@example.com'],
            ['name' => 'Bob Chen', 'email' => 'bob@example.com'],
            ['name' => 'Carol Davis', 'email' => 'carol@example.com'],
            ['name' => 'David Lee', 'email' => 'david@example.com'],
            ['name' => 'Eva Martinez', 'email' => 'eva@example.com'],
        ];

        foreach ($employees as $emp) {
            User::firstOrCreate(
                ['email' => $emp['email']],
                [
                    'name' => $emp['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'employee',
                    'kpi_score' => rand(0, 100),
                ]
            );
        }
    }
}