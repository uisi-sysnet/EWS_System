<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Location extends Model
{
    use HasFactory;
    use LogsActivity; // Add this trait

    protected $fillable = ['location_name'];

    /**
     * Configure activity logging for Location model
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['location_name'])
            ->logOnlyDirty()          // Only log when values change
            ->dontSubmitEmptyLogs()   // Don't log if nothing changed
            ->logFillable()           // Log all fillable attributes
            ->useLogName('Location'); // Group logs under 'location' name
    }

    /**
     * Mutator to trim location name
     */
    public function setNameAttribute($value)
    {
        $this->attributes['location_name'] = trim($value);
    }
}