<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = ['user_id', 'datetime', 'message', 'type'];

    protected $casts = [
        'datetime' => 'datetime',
    ];


    public function user()
{
    return $this->belongsTo(User::class);
}
}