<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use LogsActivity;
    
    protected $fillable = [ 
        'first_name',
        'last_name',
        'full_name',
        'position',
        'username',
        'email',
        'password',
        'contact_number',
        'user_level',
        'active',
        'first_login',
        'revoked',
        'last_login', // Add this
    ];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'first_name',
                'last_name', 
                'full_name',    
                'position',
                'username',
                'email',
                'contact_number',
                'user_level',
                'active',
                'first_login',
                'revoked',
                'last_login', // Add this to track login changes
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('User');  
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }
    
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
    
    public function isUser(): bool
    {
        return $this->hasRole('user');
    }


}