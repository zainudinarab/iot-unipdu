<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ruangan;
use App\Models\Lantai;

class RuanganSeeder extends Seeder
{
    public function run()
    {
        // Mendapatkan semua lantai
        $lantais = Lantai::all();
        foreach ($lantais as $lantai) {
            // Menambahkan 5 ruangan untuk setiap lantai (misalnya)
            for ($i = 1; $i <= 5; $i++) {
                // Generate nomor kelas dengan format dua digit
                $nomerkelas = str_pad($i, 2, '0', STR_PAD_LEFT);
                // Format nama ruangan: Gedung + Lantai + Nomor Ruangan
                $name = "{$lantai->gedung->nama}{$lantai->nomor}{$nomerkelas}"; // Contoh: Ruangan A2-01
                Ruangan::create([
                    'lantai_id' => $lantai->id,
                    'gedung_id' => $lantai->gedung_id, // Hubungkan ke gedung melalui lantai
                    'name' => $name, // Menggunakan nama yang telah diformat
                ]);
            }
        }
    }
}
