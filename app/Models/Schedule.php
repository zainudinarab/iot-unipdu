<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'ruangan_id',
        'relay_mask',
        'grup_id',
        'on_time',
        'off_time',
        'days',
    ];

    protected $casts = [
        'on_time' => 'integer',
        'off_time' => 'integer',
        'days' => 'integer',
    ];

    // Mutators
    public function setOnTimeAttribute($value)
    {
        $this->attributes['on_time'] = self::parseTimeToMinutes($value);
    }

    public function setOffTimeAttribute($value)
    {
        $this->attributes['off_time'] = self::parseTimeToMinutes($value);
    }

    // Accessors
    public function getOnTimeAttribute($value)
    {
        return self::formatMinutesToTime($value);
    }

    public function getOffTimeAttribute($value)
    {
        return self::formatMinutesToTime($value);
    }

    // Static conversion methods
    public static function parseTimeToMinutes($time)
    {
        if (is_numeric($time)) {
            return (int)$time; // Jika sudah dalam format menit
        }

        if (!is_string($time) || !str_contains($time, ':')) {
            return 0;
        }

        [$hours, $minutes] = explode(':', $time);
        return ((int)$hours * 60) + (int)$minutes;
    }

    public static function formatMinutesToTime($minutes)
    {
        $minutes = (int)$minutes;
        return sprintf('%02d:%02d', floor($minutes / 60), $minutes % 60);
    }

    // Bitmask operations for days
    public function setDaysAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['days'] = $this->daysToBitmask($value);
        } elseif (is_numeric($value)) {
            $this->attributes['days'] = (int) $value;
        } else {
            $this->attributes['days'] = 0; // Default jika ada kesalahan
        }
    }

    public function getDaysAttribute($value)
    {
        return $this->bitmaskToDays($value);
    }

    private function daysToBitmask($daysArray)
    {
        $bitmask = 0;
        foreach ($daysArray as $day) {
            if (ctype_digit((string) $day) && $day >= 0 && $day <= 6) { // Validasi angka 0-6
                $bitmask |= (1 << (int) $day);
            }
        }
        return $bitmask;
    }

    private function bitmaskToDays($bitmask)
    {
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            if ($bitmask & (1 << $i)) {
                $days[] = $i;
            }
        }
        return $days;
    }


    // Relationships
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
}
