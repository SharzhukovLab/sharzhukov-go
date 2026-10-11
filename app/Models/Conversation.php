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

    /**
     * Найти существующий диалог между двумя пользователями или создать.
     */
    public static function findOrCreateBetween(int $userA, int $userB): self
    {
        // Ищем разговор где участвуют оба юзера
        $existing = static::query()
            ->whereHas('users', fn($q) => $q->where('users.id', $userA))
            ->whereHas('users', fn($q) => $q->where('users.id', $userB))
            ->withCount('users')
            ->get()
            ->first(fn($c) => $c->users_count === 2);

        if ($existing) return $existing;

        $conv = static::create(['last_message_at' => now()]);
        $conv->users()->attach([$userA, $userB]);
        return $conv;
    }

    /**
     * Число непрочитанных для юзера.
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
     * Отметить всё прочитанным для юзера.
     */
    public function markReadFor(int $userId): void
    {
        $this->users()->updateExistingPivot($userId, ['last_read_at' => now()]);
    }
}