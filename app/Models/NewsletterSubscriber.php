<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email', 'name', 'status', 'confirmation_token',
        'confirmed_at', 'unsubscribed_at', 'source', 'locale', 'ip_address',
    ];

    protected $casts = [
        'confirmed_at'    => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    public static function generateToken(): string
    {
        return hash('sha256', Str::random(40) . microtime(true));
    }

    public function confirm(): void
    {
        $this->update([
            'status'           => 'confirmed',
            'confirmed_at'     => now(),
            'confirmation_token' => null,
        ]);
    }

    public function unsubscribe(): void
    {
        $this->update([
            'status'          => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);
    }
}