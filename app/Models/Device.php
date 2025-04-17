<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mac_address',
        'mqtt_topic',
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
            ->withPivot('group_index')
            ->withTimestamps();
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
