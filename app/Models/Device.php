<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'device_type',
        'device_model',
        'mac_address',
        'mqtt_topic',
        'location',
        'firmware_version',
        'ip_address',
        'status',
        'sys'
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
    // public function ruangans()
    // {
    //     return $this->belongsToMany(Ruangan::class, 'device_ruangans')->withTimestamps();
    // }
    public function ruangans()
    {
        return $this->belongsToMany(Ruangan::class, 'device_ruangans')
            ->withPivot(['group_index', 'status']) // <- status diambil juga
            ->withTimestamps();
    }
    // Relasi dengan device_ac_ir
    public function acIrData()
    {
        return $this->hasMany(DeviceIr::class);
    }

    public function canAddRuangan()
    {
        return $this->ruangans()->count() < 2; // Maksimal 2 ruangan
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($device) {
            if (!$device->mqtt_topic) {
                $device->mqtt_topic = 'Device-' . $device->id;
                $device->save();
            }
        });
    }
}
