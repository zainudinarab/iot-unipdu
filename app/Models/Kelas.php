<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = ['lantai_id', 'nomor'];

    public function lantai()
    {
        return $this->belongsTo(Lantai::class);
    }

    public function perangkat()
    {
        return $this->hasMany(Perangkat::class);
    }

    public function aksesKartu()
    {
        return $this->hasMany(AksesKartu::class);
    }

    public function logRFID()
    {
        return $this->hasMany(RFIDLog::class);
    }
}
