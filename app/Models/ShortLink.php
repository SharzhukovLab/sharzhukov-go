<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ShortLink extends Model
{
    protected $fillable = [
        'user_id', 'slug', 'target_url', 'description', 'category',
        'is_active', 'is_reserved', 'expires_at', 'clicks_count', 'source',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'is_reserved'  => 'boolean',
        'expires_at'   => 'datetime',
        'clicks_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(LinkClick::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getShortUrlAttribute(): string
    {
        return url('/s/' . $this->slug);
    }

    public function getQrUrlAttribute(): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($this->short_url);
    }
}
