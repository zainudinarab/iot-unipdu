<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceIr extends Model
{
    protected $fillable = ['device_id', 'ac_index', 'rawDataOn', 'rawDataOff'];

    // Cast rawDataOn dan rawDataOff sebagai array
    protected $casts = [
        'rawDataOn' => 'array',
        'rawDataOff' => 'array',
    ];


    // Relasi dengan device
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
