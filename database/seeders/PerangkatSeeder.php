<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perangkat;
use App\Models\Kelas;

class PerangkatSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua data Kelas
        $kelas = Kelas::all();

        foreach ($kelas as $k) {
            // Mengambil nomor urut terakhir untuk kelas dan kategori yang sama
            $lastNumber = Perangkat::where('kelas_id', $k->id)
                ->where('kategori', 'ac') // Atau kategori lain yang sesuai
                ->max('nomor_urut');

            // Menetapkan nomor urut berikutnya
            $nomorUrut = $lastNumber ? $lastNumber + 1 : 1;

            // Membuat perangkat untuk setiap kelas
            $perangkat = Perangkat::create([
                'kelas_id' => $k->id,
                'tipe' => 'relay', // Misalnya tipe 'relay', sesuaikan dengan data yang sesuai
                'nama' => 'Perangkat ' . $k->id, // Sesuaikan nama perangkat sesuai kelas
                'kategori' => 'ac', // Misalnya kategori 'ac', sesuaikan dengan data yang sesuai
                'nomor_urut' => $nomorUrut,
                'topic_mqtt' => '', // Kosongkan dahulu, nanti akan diupdate
            ]);

            // Mengupdate kolom topic_mqtt menggunakan accessor
            $perangkat->topic_mqtt = $perangkat->mqttTopic; // Memanggil accessor untuk mendapatkan mqtt_topic
            $perangkat->save(); // Menyimpan nilai mqtt_topic ke dalam database
        }
    }
}
