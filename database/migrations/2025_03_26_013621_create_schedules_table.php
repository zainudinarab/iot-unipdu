<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
            // grup_id
            $table->unsignedInteger('grup_id');  // ID grup relay (misal 1, 2, 3)
            $table->smallInteger('on_time');  // Waktu nyala dalam menit (misal 08:00 = 480)
            $table->smallInteger('off_time'); // Waktu mati dalam menit
            $table->unsignedTinyInteger('days')->default(0);
            $table->timestamps();
        });
        Schema::create('jadwal_ruangans', function (Blueprint $table) {
            $table->id();
            $table->string('ruangan');
            $table->time('on_time');
            $table->time('off_time');
            $table->unsignedTinyInteger('days'); // cukup untuk angka 0–6 (Minggu–Sabtu)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('jadwal_ruangans');
    }
};
