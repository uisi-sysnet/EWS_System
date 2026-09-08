<?php
// app/Models/BeaconStatusLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeaconStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'beacon_id',
        'beacon_identifier',   
        'beacon_name',       
        'status',
        'event_time',
        'duration_seconds',
    ];

    protected $casts = [
        'status' => 'string', 
        'event_time' => 'datetime',
        'duration_seconds' => 'integer',
    ];

    public function beacon()
    {
        return $this->belongsTo(Beacon::class, 'beacon_id', 'beacon_id');
    }
}
