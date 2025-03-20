<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfidCard extends Model
{
    use HasFactory;

    protected $fillable = ['uid', 'pemilik'];

    public function aksesKelas()
    {
        return $this->hasMany(AksesKartu::class);
    }

    public function logRFID()
    {
        return $this->hasMany(RFIDLog::class);
    }
}
