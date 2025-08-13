<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fax;
use App\Models\Numero;

class FaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample numeros first
        $numeros = [
            ['NDappel' => 'FAX001', 'Position' => 'Position 1'],
            ['NDappel' => 'FAX002', 'Position' => 'Position 2'],
            ['NDappel' => 'FAX003', 'Position' => 'Position 3'],
            ['NDappel' => 'PHONE001', 'Position' => 'Position 4'],
            ['NDappel' => 'PHONE002', 'Position' => 'Position 5'],
        ];

        foreach ($numeros as $numeroData) {
            Numero::firstOrCreate(
                ['NDappel' => $numeroData['NDappel']],
                $numeroData
            );
        }

        // Create some sample faxes
        $faxes = [
            ['NDappel' => 'FAX001', 'description' => 'Fax principal'],
            ['NDappel' => 'FAX002', 'description' => 'Fax secondaire'],
            ['NDappel' => 'FAX003', 'description' => 'Fax d\'urgence'],
        ];

        foreach ($faxes as $faxData) {
            Fax::firstOrCreate(
                ['NDappel' => $faxData['NDappel']],
                $faxData
            );
        }

        $this->command->info('Sample faxes and numeros created successfully!');
    }
}
