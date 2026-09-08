<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Signal extends Model
{
    use HasFactory;
    use LogsActivity; // Add this trait

    protected $fillable = [
        'name',
        'oid',
        'signal_id',
        'duration',
        'beacon_color_1',
        'delay_1',
        'beacon_color_2',
        'delay_2',
        'button_color',
        'description',
    ];

    protected $casts = [
        'delay_1' => 'integer',
        'delay_2' => 'integer',
        'duration'  => 'integer',
    ];

    /**
     * Configure activity logging for Signal model
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'oid',
                'signal_id',
                'duration',
                'beacon_color_1',
                'delay_1',
                'beacon_color_2',
                'delay_2',
                'button_color',
            ])
            ->logOnlyDirty()          // Only log when values change
            ->dontSubmitEmptyLogs()   // Don't log if nothing changed
            ->logFillable()           // Log all fillable attributes
            ->useLogName('Signal');   // Group logs under 'signal' name
    }
}