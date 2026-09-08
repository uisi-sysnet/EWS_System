<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;      // ADD THIS
use Spatie\Activitylog\LogOptions;                // ADD THIS

class ApiConnection extends Model
{
    use HasFactory;
    use LogsActivity;                              // ADD THIS

    protected $table = 'api_connections';

    protected $fillable = [
        'name',
        'endpoint_url',
        'header_name',
        'header_value',
        'user_id',
        'expires_at',
        'revoked',
    ];

    protected $hidden = ['header_value'];

    protected $casts = [
        'header_value' => 'encrypted',
    ];

    // ADD THIS METHOD
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'endpoint_url',
                'header_name',
                'user_id',
                'expires_at',
                'revoked',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Api Connection');
    }

    // Your existing methods (keep as is)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}