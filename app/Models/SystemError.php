<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemError extends Model
{
    protected $table = 'system_errors';
    public $timestamps = false;

    protected $fillable = [
        'error_level', 'error_code', 'message', 'file', 'line',
        'trace', 'url', 'ip_address', 'user_agent', 'user_id', 'created_at', 'is_resolved',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'created_at' => 'datetime',
    ];
}
