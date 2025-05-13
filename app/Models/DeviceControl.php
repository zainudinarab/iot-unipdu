<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceControl extends Model
{
    protected $fillable = [
        'device_id',
        'ruangan_id',
        'index',
        'group_index',
        'type',
        'name',
        'status'

    ];

    public function device()
    {
        return $this->belongsTo(Device::class)->withDefault();
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function commands()
    {
        return $this->hasMany(ControlCommand::class);
    }

    // Scopes
    public function scopeIR($query)
    {
        return $query->where('type', 'IR');
    }

    public function scopeRelay($query)
    {
        return $query->where('type', 'RELAY');
    }

    public function scopeSensor($query)
    {
        return $query->where('type', 'SENSOR');
    }


    // public static function getNextIndex($deviceId, $type, $groupIndex)
    // {
    //     // Cari kontrol terakhir berdasarkan group_index, device_id, dan type
    //     $lastIndex = self::where('device_id', $deviceId)
    //         ->where('type', $type)
    //         ->whereHas('ruangan', function ($query) use ($groupIndex) {
    //             $query->wherePivot('group_index', $groupIndex); // Sesuaikan dengan group_index
    //         })
    //         ->orderByDesc('index')
    //         ->value('index');

    //     // Tentukan index baru (0 jika tidak ada kontrol, atau index terakhir + 1)
    //     return is_null($lastIndex) ? $groupIndex * 2 : $lastIndex + 1; // Mulai dengan index sesuai group_index
    // }
    // public static function reindexDeviceType($deviceId, $type, $groupIndex)
    // {
    //     // Ambil kontrol yang sesuai dengan device_id, type, dan group_index
    //     $controls = self::where('device_id', $deviceId)
    //         ->where('type', $type)
    //         ->whereHas('ruangan', function ($query) use ($groupIndex) {
    //             $query->wherePivot('group_index', $groupIndex);
    //         })
    //         ->orderBy('index')
    //         ->get();

    //     // Reindex kontrol perangkat berdasarkan urutan yang benar
    //     $index = $groupIndex * 2; // Mulai dengan index yang benar
    //     foreach ($controls as $control) {
    //         $control->update(['index' => $index++]); // Perbarui index
    //     }
    // }
    // protected static function booted()

    // {
    //     static::deleted(function ($control) {
    //         self::reindexDeviceType($control->device_id, $control->type, $control->ruangan->pivot->group_index);
    //     });
    // }
}
