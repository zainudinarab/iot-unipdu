<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lantai;

class LantaiSeeder extends Seeder
{
    public function run(): void
    {
        $gedungs = \App\Models\Gedung::all();

        foreach ($gedungs as $gedung) {
            for ($i = 1; $i <= 3; $i++) {
                Lantai::create([
                    'gedung_id' => $gedung->id,
                    'nomor' => $i,
                ]);
            }
        }
    }
}
