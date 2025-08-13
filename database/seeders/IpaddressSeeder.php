<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IpaddressSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 300; $i++) {
            DB::table('ip_addresses')->insert([
                'ipAdresses' => '192.168.1.' . rand(1, 254),
                'destination' => 'Destination ' . $i,
                'organisme' => 'Organisme ' . $i,
                'Application' => 'App ' . rand(1, 5),
                'port' => rand(1000, 9999),
                'mask' => 24,
                'note' => 'Note ' . $i,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
         /*for ($i = 1; $i <= 254; $i++) {
            plage::create([
                'ipAdresses' => "192.168.1.$i",
                'direction' => 'Direction ' . $i,
            ]);
        }*/
    }
    
}
