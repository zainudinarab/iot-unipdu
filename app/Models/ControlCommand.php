<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlCommand extends Model
{
    protected $fillable = ['device_control_id', 'command_type', 'data'];
    // Laravel >= 9 (casts)
    protected $casts = [
        'data' => 'array',
    ];


    public function control()
    {
        return $this->belongsTo(DeviceControl::class, 'device_control_id');
    }
    public function deviceControl()
    {
        return $this->belongsTo(DeviceControl::class);
    }
}
