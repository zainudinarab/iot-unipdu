<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $lantais = \App\Models\Lantai::all();

        foreach ($lantais as $lantai) {
            for ($i = 1; $i <= 3; $i++) {
                Kelas::create([
                    'lantai_id' => $lantai->id,
                    'nomor' => rand(201, 210), // Nomor kelas acak
                ]);
            }
        }
    }
}
