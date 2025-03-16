<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perangkat extends Model
{
    protected $table = 'perangkat';
    // Kolom yang dapat diisi
    protected $fillable = ['ruangan_id', 'nama', 'topik_mqtt'];
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function status()
    {
        return $this->hasOne(StatusPerangkat::class);
    }
}
