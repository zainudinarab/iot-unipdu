<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perangkat extends Model
{

    // Kolom yang dapat diisi
    protected $fillable = ['ruangan_id', 'tipe', 'nama', 'kategori', 'nomor_urut', 'topic_mqtt', 'status', 'data_tambahan'];
    // Cast kolom `data_tambahan` ke tipe array
    protected $casts = [
        'data_tambahan' => 'array',
    ];
    public function status()
    {
        return $this->hasOne(StatusPerangkat::class);
    }
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function kodeIR()
    {
        return $this->hasMany(KodeIR::class);
    }
    // public function getMqttTopicAttribute()
    // {
    //     $gedung = $this->ruangan->lantai->gedung->nama;
    //     $lantai = $this->ruangan->lantai->nomor;
    //     $ruangan = $this->ruangan->name;
    //     $tipe = strtolower($this->tipe); // relay, sensor, ir
    //     $kategori = strtolower($this->kategori); // ac, lampu, arus
    //     $nomor = $this->nomor_urut; // Nomor urut dalam kelas
    //     return "{$gedung}/{$lantai}/{$ruangan}/{$tipe}/{$kategori}{$nomor}";
    // }
    // protected static function boot()
    // {
    //     parent::boot();
    //     static::saving(function ($perangkat) {
    //         // Mengisi kolom mqtt_topic sebelum menyimpan data
    //         $perangkat->topic_mqtt = $perangkat->mqttTopic; // Menggunakan accessor untuk menghasilkan mqtt_topic
    //     });
    // }
}
