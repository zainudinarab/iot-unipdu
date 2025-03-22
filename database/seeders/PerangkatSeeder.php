<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perangkat;
use App\Models\Ruangan;
use Faker\Factory as Faker;

class PerangkatSeeder extends Seeder
{
    public function run(): void
    {
        $ruangans = Ruangan::all();
        $faker = Faker::create();

        foreach ($ruangans as $ruangan) {

            for ($i = 1; $i <= 3; $i++) {
                $kategori = $faker->randomElement(['AC', 'Lampu', 'sensor_arus']);
                $tipe = $faker->randomElement(['relay', 'sensor']);
                // Mengambil nomor urut perangkat terakhir berdasarkan kategori di dalam ruangan
                $lastNumber = Perangkat::where('ruangan_id', $ruangan->id)
                    ->where('kategori', $kategori) // Mengambil berdasarkan kategori perangkat
                    ->max('nomor_urut');

                // Jika perangkat sudah ada, increment nomor urutnya, jika tidak, mulai dari 1
                $nomorUrut = $lastNumber ? $lastNumber + 1 : 1;
                $perangkat = Perangkat::create([
                    'ruangan_id' => $ruangan->id, // Menghubungkan perangkat ke ruangan
                    'tipe' => $tipe,
                    'nama' => $tipe . '' . $kategori . ' ' . $nomorUrut,
                    'kategori' =>  $kategori, // Kategori perangkat
                    'nomor_urut' => $nomorUrut, // Nomor urut perangkat dalam ruangan
                    'topic_mqtt' => '',
                    'status' => $faker->boolean(), // Status perangkat acak (aktif/tidak aktif)
                ]);
                // $perangkat->topic_mqtt = $perangkat->mqttTopic;
                $perangkat->save();
            }
        }
    }
}
