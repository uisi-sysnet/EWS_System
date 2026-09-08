<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Siren extends Model
{
    use HasFactory;
    use LogsActivity; // Add this trait

    protected $fillable = [
        'name',
        'location_id',
        'oid',
        'siren_id',
        'group',
        'latitude',
        'longitude',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Configure activity logging for Siren model
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'location_id',
                'oid',
                'siren_id',
                'group',
                'latitude',
                'longitude',
                'enabled',
            ])
            ->logOnlyDirty()          // Only log when values change
            ->dontSubmitEmptyLogs()   // Don't log if nothing changed
            ->logFillable()           // Log all fillable attributes
            ->useLogName('Siren');    // Group logs under 'siren' name
    }

    /**
     * Get the location that owns the siren
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}