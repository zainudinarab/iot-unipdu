<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perangkat extends Model
{
    protected $table = 'perangkat';
    // Kolom yang dapat diisi
    protected $fillable = ['kelas_id', 'tipe', 'nama', 'kategori', 'nomor_urut', 'topic_mqtt','status'];


    public function status()
    {
        return $this->hasOne(StatusPerangkat::class);
    }
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function kodeIR()
    {
        return $this->hasMany(KodeIR::class);
    }
    public function getMqttTopicAttribute()
    {
        $gedung = $this->kelas->lantai->gedung->nama;
        $lantai = $this->kelas->lantai->id;
        $kelas = $this->kelas->nomor;
        $tipe = strtolower($this->tipe); // relay, sensor, ir
        $kategori = strtolower($this->kategori); // ac, lampu, arus
        $nomor = $this->nomor_urut; // Nomor urut dalam kelas
        return "gedung-{$gedung}/lantai-{$lantai}/kelas-{$kelas}/{$tipe}/{$kategori}{$nomor}";
    }
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($perangkat) {
            // Mengisi kolom mqtt_topic sebelum menyimpan data
            $perangkat->topic_mqtt = $perangkat->mqttTopic; // Menggunakan accessor untuk menghasilkan mqtt_topic
        });
    }
}
