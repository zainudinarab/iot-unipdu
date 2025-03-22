<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gedung;
use App\Models\Lantai;

class GedungLantaiSeeder extends Seeder
{
    public function run()
    {
        // Data gedung yang akan dimasukkan
        $gedungs = [
            ['nama' => 'A', 'keterangan' => 'Gedung A', 'jumlah_lantai' => 3],
            ['nama' => 'B', 'keterangan' => 'Gedung B', 'jumlah_lantai' => 2],
            ['nama' => 'C', 'keterangan' => 'Gedung C', 'jumlah_lantai' => 2],
        ];

        // Menambahkan gedung dan lantai dalam satu proses
        foreach ($gedungs as $gedungData) {
            // Menambahkan gedung
            $gedung = Gedung::create($gedungData);
            // Menambahkan 3 lantai untuk setiap gedung
            for ($i = 1; $i <= $gedungData['jumlah_lantai']; $i++) {
                Lantai::create([
                    'gedung_id' => $gedung->id,
                    'nomor' => $i,
                ]);
            }
        }
    }
}
