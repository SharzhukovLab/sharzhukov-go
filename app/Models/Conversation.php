<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = ['last_message_at'];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withPivot('last_read_at');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function lastMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest()->limit(1);
    }

    /**
     * Найти или создать диалог между двумя пользователями.
     */
    public static function findOrCreateBetween(int $userA, int $userB): self
    {
        // Ищем существующий разговор где участвуют оба
        $existing = static::whereHas('users', fn($q) => $q->where('users.id', $userA))
            ->whereHas('users', fn($q) => $q->where('users.id', $userB))
            ->where('conversation_user', '!=', null) // страховка
            ->withCount('users')
            ->having('users_count', 2)
            ->first();

        if ($existing) return $existing;

        $conv = static::create(['last_message_at' => now()]);
        $conv->users()->attach([$userA, $userB]);
        return $conv;
    }

    /**
     * Считает непрочитанные для юзера в этом диалоге.
     */
    public function unreadCountFor(int $userId): int
    {
        $pivot = $this->users()->where('users.id', $userId)->first()?->pivot;
        $lastRead = $pivot?->last_read_at;

        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->when($lastRead, fn($q) => $q->where('created_at', '>', $lastRead))
            ->count();
    }

    /**
     * Помечает всё прочитанным для юзера.
     */
    public function markReadFor(int $userId): void
    {
        $this->users()->updateExistingPivot($userId, ['last_read_at' => now()]);
    }
}