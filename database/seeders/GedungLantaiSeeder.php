<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Ruangan;


class GedungLantaiSeeder extends Seeder
{
    public function run()
    {
        // Data gedung, jumlah lantai, dan jumlah ruangan per lantai
        $gedungs = [
            'A' => [
                'keterangan' => 'Gedung FIK',
                'lantai' => [
                    1 => 7, // lantai 1, 10 ruangan
                    2 => 7, // lantai 2, 12 ruangan
                ],
            ],
            'B' => [
                'keterangan' => 'Gedung Santek',
                'lantai' => [
                    1 => 4,
                    2 => 4,
                ],
            ],
            'G' => [
                'keterangan' => 'Gedung Graha',
                'lantai' => [
                    1 => 2,
                    2 => 4,
                    3 => 4,
                ],
            ],
            'U' => [
                'keterangan' => 'Gedung Kampus Utama',
                'lantai' => [
                    1 => 2,   // lantai 1, 2 ruangan
                    2 => 14,  // lantai 2, 14 ruangan
                    3 => 14,  // lantai 3, 14 ruangan
                ],
            ],
        ];

        foreach ($gedungs as $namaGedung => $gedungData) {
            $gedung = Gedung::create([
                'nama' => $namaGedung,
                'keterangan' => $gedungData['keterangan'],
                'jumlah_lantai' => count($gedungData['lantai']), // Tambahkan ini
            ]);

            foreach ($gedungData['lantai'] as $nomorLantai => $jumlahRuangan) {
                $lantai = Lantai::create([
                    'gedung_id' => $gedung->id,
                    'nomor' => $nomorLantai,
                ]);

                for ($i = 1; $i <= $jumlahRuangan; $i++) {
                    $nomorKelas = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $namaRuangan = "{$namaGedung}{$nomorLantai}{$nomorKelas}"; // e.g., A110, U302

                    Ruangan::create([
                        'gedung_id' => $gedung->id,
                        'lantai_id' => $lantai->id,
                        'name' => $namaRuangan,
                    ]);
                }
            }
        }
    }
}
