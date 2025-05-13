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
        Schema::create('device_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruangan_id')->constrained()->onDelete('cascade'); // relasi ke ruangan
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete(); // <= di sini
            $table->unsignedTinyInteger('index'); // Index global per device berdasarkan group_index dan type
            $table->unsignedTinyInteger('group_index'); // duplikat dari device_ruangans
            $table->enum('type', ['RELAY', 'IR', 'SENSOR']);
            $table->string('name');
            $table->boolean('status')->nullable();
            $table->timestamps();

            // unik per kombinasi type + index (diasumsikan satu device per ruangan)
            // $table->unique(['device_id', 'type', 'index']);
        });


        Schema::create('control_commands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_control_id')->constrained()->onDelete('cascade');
            $table->enum('command_type', ['ON', 'OFF']);
            $table->text('data'); // IR code (string, hex, json, dsb)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_commands');
        Schema::dropIfExists('device_controls');
    }
};
