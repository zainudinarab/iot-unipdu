<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Tabel Gedung
        Schema::create('gedungs', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); // Contoh: Gedung A, Gedung B
            $table->string('keterangan')->nullable();
            $table->integer('jumlah_lantai');  // Menambahkan jumlah lantai
            $table->timestamps();
        });

        // Tabel Lantai
        Schema::create('lantais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gedung_id')->constrained()->onDelete('cascade');
            $table->integer('nomor'); // 1, 2, 3
            $table->timestamps();
        });
        // Tabel Kelas
        Schema::create('ruangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lantai_id')->constrained()->onDelete('cascade');
            $table->foreignId('gedung_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
            $table->unique(['gedung_id', 'lantai_id', 'name']);
        });
        // Tabel Perangkat (Relay, Sensor Arus, IR)
        Schema::create('perangkats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruangan_id')->constrained()->onDelete('cascade');
            $table->string('tipe'); // relay, sensor
            $table->string('nama')->nullable(); // Misalnya "Relay AC 1"
            $table->string('kategori')->nullable(); // Misalnya "AC", "Lampu","sensor_arus"
            $table->integer('nomor_urut')->nullable(); // Nomor urut perangkat dalam kelas
            $table->string('topic_mqtt')->nullable(); // Topik MQTT unik
            $table->boolean('status')->default(false); // Status aktif/tidak aktif
            // Tambahkan kolom untuk data tambahan dalam format JSON
            $table->json('data_tambahan')->nullable(); // Contoh: {"kode_ir": {"on": "0x20DF10EF", "off": "0x20DF40BF"}, "suhu": 25.5}
            $table->timestamps();
        });

        // Tabel Kode IR (untuk update kode remote)
        Schema::create('kode_ir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perangkat_id')->constrained('perangkat')->onDelete('cascade');
            $table->string('kode'); // Kode IR untuk menyalakan/mematikan AC
            $table->timestamps();
        });

        // Tabel Kartu RFID
        Schema::create('rfid_cards', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique(); // UID kartu
            $table->string('pemilik'); // Nama pemilik kartu
            $table->timestamps();
        });

        // Tabel Hak Akses Kartu ke Kelas
        Schema::create('akses_kartu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfid_card_id')->constrained('rfid_cards')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Tabel Log Penggunaan Kartu
        Schema::create('rfid_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfid_card_id')->constrained('rfid_cards')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['success', 'denied']); // success = kartu valid, denied = kartu ditolak
            $table->timestamp('waktu_scan')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rfid_logs');
        Schema::dropIfExists('akses_kartu');
        Schema::dropIfExists('rfid_cards');
        Schema::dropIfExists('kode_ir');
        Schema::dropIfExists('perangkats');
        Schema::dropIfExists('ruangans');
        Schema::dropIfExists('lantais');
        Schema::dropIfExists('gedungs');
    }
};
