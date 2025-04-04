<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPerangkat extends Model
{
    protected $table = 'status_perangkat';
    protected $fillable = ['perangkat_id', 'status'];

    public function perangkat()
    {
        return $this->belongsTo(Perangkat::class);
    }
}
