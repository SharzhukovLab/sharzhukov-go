<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CookieConsent extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'categories', 'version',
        'action', 'ip_address', 'user_agent', 'locale', 'accepted_at', 'revoked_at',
    ];

    protected $casts = [
        'categories'  => 'array',
        'accepted_at' => 'datetime',
        'revoked_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}