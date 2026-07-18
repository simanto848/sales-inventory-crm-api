<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            ['name' => 'Downtown Store', 'location' => '123 Main Street, New York, NY 10001'],
            ['name' => 'Mall Location', 'location' => '456 Shopping Center Blvd, Los Angeles, CA 90001'],
            ['name' => 'Airport Branch', 'location' => '789 Terminal Way, Chicago, IL 60601'],
            ['name' => 'University Campus', 'location' => '321 College Ave, Boston, MA 02101'],
        ];

        foreach ($branches as $branch) {
            Branch::firstOrCreate(['name' => $branch['name']], $branch);
        }
    }
}