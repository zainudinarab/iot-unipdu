<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RfidCard;

class RfidCardSeeder extends Seeder
{
    public function run(): void
    {
        RfidCard::insert([
            ['uid' => 'A1B2C3D4E5', 'pemilik' => 'Ali'],
            ['uid' => 'F6G7H8I9J0', 'pemilik' => 'Budi'],
            ['uid' => 'K1L2M3N4O5', 'pemilik' => 'Citra'],
        ]);
    }
}
