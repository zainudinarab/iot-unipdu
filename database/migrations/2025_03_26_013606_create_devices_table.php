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
            $table->string('name');
            $table->string('mac_address')->unique();
            $table->string('mqtt_topic')->unique()->nullable();
            $table->boolean('sys')->default(false);
            $table->timestamps();
        });

        // Tabel Pivot Device_Ruangan
        Schema::create('device_ruangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->foreignId('ruangan_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('group_index'); // Kolom grup 0–255
            $table->timestamps();

            $table->unique(['device_id', 'ruangan_id']); // Hindari duplikasi hubungan
            $table->unique('ruangan_id'); // Pastikan ruangan hanya bisa terhubung ke satu device
            $table->unique(['device_id', 'group_index']); // Satu group index unik per device
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_ruangans');
        Schema::dropIfExists('devices');
    }
};
