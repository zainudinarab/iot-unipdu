<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gedung extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'keterangan','jumlah_lantai'];

    public function lantais()
    {
        return $this->hasMany(Lantai::class);
    }
}
