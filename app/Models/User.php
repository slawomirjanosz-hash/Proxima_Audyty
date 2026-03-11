<?php

namespace App\Models;

use App\Enums\UserRole;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    public const TAB_HOME = 'home';
    public const TAB_OFFER = 'offer';
    public const TAB_AUDITS = 'audits';
    public const TAB_CLIENT_ZONE = 'client_zone';
    public const TAB_SETTINGS = 'settings';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tab_permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'tab_permissions' => 'array',
        ];
    }

    public static function tabLabels(): array
    {
        return [
            self::TAB_HOME => 'Strona główna',
            self::TAB_OFFER => 'Oferta',
            self::TAB_AUDITS => 'Audyty',
            self::TAB_CLIENT_ZONE => 'Strefa klienta',
            self::TAB_SETTINGS => 'Ustawienia',
        ];
    }

    public function defaultTabPermissions(): array
    {
        if ($this->isSuperAdmin()) {
            return array_fill_keys(array_keys(self::tabLabels()), true);
        }

        if ($this->isClient()) {
            return [
                self::TAB_HOME => true,
                self::TAB_OFFER => true,
                self::TAB_AUDITS => true,
                self::TAB_CLIENT_ZONE => true,
                self::TAB_SETTINGS => true,
            ];
        }

        return [
            self::TAB_HOME => true,
            self::TAB_OFFER => true,
            self::TAB_AUDITS => true,
            self::TAB_CLIENT_ZONE => true,
            self::TAB_SETTINGS => true,
        ];
    }

    public function resolvedTabPermissions(): array
    {
        $defaults = $this->defaultTabPermissions();
        $saved = is_array($this->tab_permissions) ? $this->tab_permissions : [];

        return array_replace($defaults, array_intersect_key($saved, $defaults));
    }

    public function canAccessTab(string $tab): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return (bool) ($this->resolvedTabPermissions()[$tab] ?? false);
    }

    public function managedCompanies(): HasMany
    {
        return $this->hasMany(Company::class, 'auditor_id');
    }

    public function ownedCompanies(): HasMany
    {
        return $this->hasMany(Company::class, 'client_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function canManageEverything(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function isAuditor(): bool
    {
        return $this->role === UserRole::Auditor;
    }

    public function isClient(): bool
    {
        return $this->role === UserRole::Client;
    }
}
