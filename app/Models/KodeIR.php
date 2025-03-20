<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeIR extends Model
{
    use HasFactory;

    protected $fillable = ['perangkat_id', 'kode'];

    public function perangkat()
    {
        return $this->belongsTo(Perangkat::class);
    }
}
