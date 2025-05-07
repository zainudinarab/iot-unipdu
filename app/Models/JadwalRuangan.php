<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalRuangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'ruangan',
        'on_time',
        'off_time',
        'days',
    ];
}
