<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@email.com',
                'phone' => '+1-555-0101',
                'address' => '123 Main St, New York, NY 10001',
                'is_active' => true,
                'purchase_frequency' => 5,
                'last_purchase_date' => now()->subDays(15),
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.j@email.com',
                'phone' => '+1-555-0102',
                'address' => '456 Oak Ave, Los Angeles, CA 90001',
                'is_active' => true,
                'purchase_frequency' => 12,
                'last_purchase_date' => now()->subDays(5),
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'm.brown@email.com',
                'phone' => '+1-555-0103',
                'address' => '789 Pine Rd, Chicago, IL 60601',
                'is_active' => false,
                'purchase_frequency' => 2,
                'last_purchase_date' => now()->subDays(120),
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@email.com',
                'phone' => '+1-555-0104',
                'address' => '321 Elm St, Houston, TX 77001',
                'is_active' => true,
                'purchase_frequency' => 8,
                'last_purchase_date' => now()->subDays(30),
            ],
            [
                'name' => 'James Wilson',
                'email' => 'j.wilson@email.com',
                'phone' => '+1-555-0105',
                'address' => '654 Maple Dr, Phoenix, AZ 85001',
                'is_active' => false,
                'purchase_frequency' => 1,
                'last_purchase_date' => now()->subDays(200),
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@email.com',
                'phone' => '+1-555-0106',
                'address' => '987 Cedar Ln, Philadelphia, PA 19101',
                'is_active' => true,
                'purchase_frequency' => 15,
                'last_purchase_date' => now()->subDays(10),
            ],
            [
                'name' => 'Robert Taylor',
                'email' => 'robert.t@email.com',
                'phone' => '+1-555-0107',
                'address' => '147 Birch Ct, San Antonio, TX 78201',
                'is_active' => true,
                'purchase_frequency' => 3,
                'last_purchase_date' => now()->subDays(45),
            ],
            [
                'name' => 'Jennifer Martinez',
                'email' => 'j.martinez@email.com',
                'phone' => '+1-555-0108',
                'address' => '258 Spruce Blvd, San Diego, CA 92101',
                'is_active' => false,
                'purchase_frequency' => 4,
                'last_purchase_date' => now()->subDays(95),
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(['email' => $customer['email']], $customer);
        }
    }
}