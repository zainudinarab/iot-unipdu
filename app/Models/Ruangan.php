<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    protected $fillable = ['lantai_id', 'gedung_id', 'name'];

    /**
     * Mendefinisikan relasi dengan model Lantai
     */
    public function lantai()
    {
        return $this->belongsTo(Lantai::class);
    }
    /**
     * Mendefinisikan relasi dengan model Gedung
     */
    public function gedung()
    {
        return $this->belongsTo(Gedung::class);
    }
    public function perangkats()
    {
        return $this->hasMany(Perangkat::class);
    }
}
