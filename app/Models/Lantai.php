<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lantai extends Model
{
    use HasFactory;

    protected $fillable = ['gedung_id', 'nomor'];

    public function gedung()
    {
        return $this->belongsTo(Gedung::class);
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
}
