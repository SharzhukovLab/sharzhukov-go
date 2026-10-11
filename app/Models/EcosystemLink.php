<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EcosystemLink extends Model
{
    protected $fillable = ['group', 'label', 'url', 'sort_order', 'is_visible', 'is_external'];

    protected $casts = [
        'is_visible'  => 'boolean',
        'is_external' => 'boolean',
    ];

    public function scopeVisible($q) { return $q->where('is_visible', true); }
    public function scopeGroup($q, string $group) { return $q->where('group', $group); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderBy('label'); }
}