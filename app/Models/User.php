<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser, HasAvatar
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar_path',
        'bio',
        'social_links',
        'terms_accepted_at',
        'deletion_requested_at',
        'team_role',
        'team_roles_data',
        'team_year',
        'team_specs',
        'team_specs_data',
        'team_order',
        'show_in_team',
        'approved_comments_count',
        'is_trusted',
        'is_banned',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'       => 'datetime',
        'password'                => 'hashed',
        'terms_accepted_at'       => 'datetime',
        'deletion_requested_at'   => 'datetime',
        'is_trusted'              => 'boolean',
        'is_banned'               => 'boolean',
        'show_in_team'            => 'boolean',
        'team_specs_data'         => 'array',
        'team_roles_data'         => 'array',
        'social_links'            => 'array',
        'two_factor_confirmed_at' => 'datetime',
    ];

    /**
     * Полный URL аватара (или SVG-fallback с первой буквой).
     */
    /**
     * Avatar URL — real file or generated SVG placeholder with initial.
     */
        /**
     * Avatar URL — real file or generated SVG placeholder with initial.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_path) {
            return 'https://sharzhukov.com/storage/' . ltrim($this->avatar_path, '/');
        }

        $initial = strtoupper(mb_substr($this->name ?? 'U', 0, 1));
        $initial = htmlspecialchars($initial, ENT_XML1, 'UTF-8');

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128">'
             . '<rect width="128" height="128" fill="#ff5c1f"/>'
             . '<text x="64" y="84" font-family="-apple-system,BlinkMacSystemFont,sans-serif" '
             . 'font-size="60" font-weight="900" fill="#ffffff" text-anchor="middle" '
             . 'letter-spacing="-2">' . $initial . '</text>'
             . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Filament avatar URL — использует getAvatarUrlAttribute.
     */
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function getProfileUrlAttribute(): string
    {
        return route('profile.show', $this->username ?: $this->id);
    }

    /**
     * Автогенерация username при создании пользователя.
     */
    protected static function booted(): void
    {
        static::creating(function (self $user) {
            if (empty($user->username)) {
                $base = Str::slug($user->name ?: explode('@', $user->email)[0], '-');
                $user->username = $base . '-' . Str::lower(Str::random(4));
            }
        });
    }

    /**
     * Комментарии пользователя.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Реакции пользователя на комментарии.
     */
    public function commentReactions()
    {
        return $this->hasMany(CommentReaction::class);
    }

    /**
     * Проекты, загруженные пользователем.
     */
    public function projects()
    {
        return $this->hasMany(UserProject::class);
    }

    /**
     * Только одобренные проекты (для публичного отображения).
     */
    public function approvedProjects()
    {
        return $this->hasMany(UserProject::class)->where('status', 'approved');
    }

    /**
     * Доверенный ли пользователь (постит без пре-модерации).
     */
    /**
     * Разрешённые платформы для соцсетей.
     */
    public static function allowedSocialPlatforms(): array
    {
        return [
            'github'    => 'GitHub',
            'twitter'   => 'X / Twitter',
            'telegram'  => 'Telegram',
            'discord'   => 'Discord',
            'reddit'    => 'Reddit',
            'youtube'   => 'YouTube',
            'twitch'    => 'Twitch',
            'vk'        => 'VK',
            'linkedin'  => 'LinkedIn',
            'website'   => 'Personal website',
        ];
    }

    /**
     * Возможные типы ролей в команде.
     */
    public static function roleTypes(): array
    {
        return [
            'founder'           => 'Founder',
            'co_founder'        => 'Co-Founder',
            'lead_developer'    => 'Lead Developer',
            'developer'         => 'Developer',
            'frontend'          => 'Frontend Developer',
            'backend'           => 'Backend Developer',
            'fullstack'         => 'Full-Stack Developer',
            'devops'            => 'DevOps',
            'designer'          => 'Designer',
            'video_editor'      => 'Video Editor',
            'tester'            => 'Tester / QA',
            'project_manager'   => 'Project Manager',
            'community_manager' => 'Community Manager',
            'moderator'         => 'Moderator',
            'docs_writer'       => 'Documentation Writer',
            'custom'            => 'Other (custom)',
        ];
    }

    /**
     * Список ролей с метками.
     * Приоритет: team_roles_data (JSON), fallback — team_role (string, split by ·).
     */
    public function getTeamRolesListAttribute(): array
    {
        $types = self::roleTypes();
        $result = [];

        if (!empty($this->team_roles_data) && is_array($this->team_roles_data)) {
            foreach ($this->team_roles_data as $role) {
                $type = $role['type'] ?? 'custom';
                if ($type === 'custom') {
                    $label = trim($role['label'] ?? '');
                    if ($label === '') continue;
                } else {
                    $label = $types[$type] ?? ucfirst($type);
                }
                $result[] = [
                    'type'  => $type,
                    'label' => $label,
                ];
            }
            return $result;
        }

        // Fallback: старый формат "A · B"
        if (!empty($this->team_role)) {
            $parts = array_map('trim', explode('·', $this->team_role));
            foreach ($parts as $part) {
                if ($part === '') continue;
                $result[] = [
                    'type'  => 'custom',
                    'label' => $part,
                ];
            }
        }

        return $result;
    }

    /**
     * Возможные типы модулей характеристик.
     */
    public static function specTypes(): array
    {
        return [
            'desktop'  => ['label' => 'Desktop',  'icon' => '🖥'],
            'laptop'   => ['label' => 'Laptop',   'icon' => '💻'],
            'cpu'      => ['label' => 'CPU',      'icon' => '⚙️'],
            'gpu'      => ['label' => 'GPU',      'icon' => '🎮'],
            'ram'      => ['label' => 'RAM',      'icon' => '🧠'],
            'storage'  => ['label' => 'Storage',  'icon' => '💾'],
            'os'       => ['label' => 'OS',       'icon' => '🪟'],
            'display'  => ['label' => 'Display',  'icon' => '📺'],
            'keyboard' => ['label' => 'Keyboard', 'icon' => '⌨️'],
            'mouse'    => ['label' => 'Mouse',    'icon' => '🖱'],
            'custom'   => ['label' => 'Other',    'icon' => '✨'],
        ];
    }

    /**
     * Список характеристик с человекочитаемыми метками и иконками.
     * Приоритет: team_specs_data (JSON), fallback — team_specs (string, split by ·).
     */
    public function getTeamSpecsListAttribute(): array
    {
        $types = self::specTypes();
        $result = [];

        // Новая структура (JSON)
        if (!empty($this->team_specs_data) && is_array($this->team_specs_data)) {
            foreach ($this->team_specs_data as $spec) {
                $type = $spec['type'] ?? 'custom';
                $value = trim($spec['value'] ?? '');
                if ($value === '') continue;

                $result[] = [
                    'type'  => $type,
                    'label' => $types[$type]['label'] ?? 'Other',
                    'icon'  => $types[$type]['icon'] ?? '✨',
                    'value' => $value,
                ];
            }
            return $result;
        }

        // Fallback: старый формат "A · B · C"
        if (!empty($this->team_specs)) {
            $parts = array_map('trim', explode('·', $this->team_specs));
            foreach ($parts as $part) {
                if ($part === '') continue;
                $result[] = [
                    'type'  => 'custom',
                    'label' => null,
                    'icon'  => '✨',
                    'value' => $part,
                ];
            }
        }

        return $result;
    }

    /**
     * Соцсети с человекочитаемыми названиями.
     */
    public function getSocialLinksListAttribute(): array
    {
        $platforms = self::allowedSocialPlatforms();
        $links = $this->social_links ?? [];
        return array_map(function ($link) use ($platforms) {
            return [
                'platform' => $link['platform'] ?? '',
                'label'    => $platforms[$link['platform'] ?? ''] ?? ucfirst($link['platform'] ?? ''),
                'url'      => $link['url'] ?? '',
            ];
        }, $links);
    }

    public function isMember(): bool
    {
        return in_array($this->role, ['member', 'moderator', 'editor', 'admin'], true);
    }

    public function isTrusted(): bool
    {
        return $this->is_trusted
            && $this->hasVerifiedEmail()
            && !$this->is_banned;
    }

    /**
     * Скоуп: участники команды для отображения на About.
     */
    public function scopeTeam($query)
    {
        return $query->where('show_in_team', true)->orderBy('team_order');
    }

    /**
     * Отправка кастомного письма верификации.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification);
    }

    /**
     * Может ли публиковать комментарии.
     */
    public function canComment(): bool
    {
        return $this->hasVerifiedEmail() && !$this->is_banned;
    }

    // ============================================
    // ROLES
    // ============================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isModerator(): bool
    {
        return in_array($this->role, ['moderator', 'editor', 'admin'], true);
    }

    public function isEditor(): bool
    {
        return in_array($this->role, ['editor', 'admin'], true);
    }

    public function canModerate(): bool
    {
        return $this->isModerator();
    }

    public function canEdit(): bool
    {
        return $this->isEditor();
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->isModerator();
    }
}