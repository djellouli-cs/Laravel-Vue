<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\plage;
use App\Models\ipAddresses;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*
        for ($i = 1; $i <= 10; $i++) {
            plage::create([
                'ipAdresses' => "192.168.1.$i",
                'direction' => 'Direction ' . $i,
            ]);
        }*/

        // ✅ Call the custom IP address seeder
        $this->call([
            IpaddressSeeder::class,
        ]);
    }
}
