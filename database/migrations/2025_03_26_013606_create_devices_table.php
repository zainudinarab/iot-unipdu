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
        // Tabel Devices
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama atau alias perangkat
            $table->string('device_type')->nullable(); // Jenis perangkat (ESP32, Arduino, dsb)
            $table->string('device_model')->nullable(); // Contoh isi: ESP32-S3, ESP32-C3, dll
            $table->string('mac_address')->unique(); // Alamat MAC unik
            $table->string('mqtt_topic')->unique()->nullable(); // Topik MQTT yang digunakan
            $table->string('location')->nullable(); // Lokasi fisik (misal: "Ruang Server", "Lab 1")
            $table->string('firmware_version')->nullable(); // Versi firmware saat ini
            $table->string('ip_address')->nullable(); // IP saat ini (jika tersedia)
            $table->string('status')->default('offline'); // Status: online/offline/error
            $table->boolean('sys')->default(false); // Flag sistem
            $table->timestamps();
        });


        // Tabel Pivot Device_Ruangan
        Schema::create('device_ruangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->foreignId('ruangan_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('group_index'); // Kolom grup 0–255
            $table->boolean('status')->default(false); // Tambahkan ini (false = OFF, true = ON)
            $table->timestamps();

            $table->unique(['device_id', 'ruangan_id']); // Hindari duplikasi hubungan
            $table->unique('ruangan_id'); // Pastikan ruangan hanya bisa terhubung ke satu device
            $table->unique(['device_id', 'group_index']); // Satu group index unik per device
        });
        Schema::create('device_irs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices');  // Menghubungkan ke device_id di tabel 'devices'
            $table->integer('ac_index'); // Indeks AC (0-5 untuk 6 unit AC)
            $table->json('rawDataOn');   // Data raw untuk ON (dalam format array)
            $table->json('rawDataOff');  // Data raw untuk OFF (dalam format array)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_irs');
        Schema::dropIfExists('device_ruangans');
        Schema::dropIfExists('devices');
    }
};
