<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RFIDLog extends Model
{
    use HasFactory;

    protected $fillable = ['rfid_card_id', 'kelas_id', 'status', 'waktu_scan'];

    public function kartu()
    {
        return $this->belongsTo(RFIDCard::class, 'rfid_card_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
