<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    public static function normalizeLegalForm(string $name): string
    {
        $normalized = trim($name);

        $patterns = [
            '/sp[óo]łka\s+z\s+ograniczon[ąa]\s+odpowiedzialno[sś]ci[ąa]/iu' => 'sp. z o. o.',
            '/sp[óo]łka\s+cywilna/iu' => 'S.C.',
            '/sp[óo]łka\s+jawna/iu' => 'S.J.',
            '/sp[óo]łka\s+komandytowa/iu' => 'S.K.',
            '/sp[óo]łka\s+komandytowo\s*-\s*akcyjna/iu' => 'S.K.A.',
            '/sp[óo]łka\s+akcyjna/iu' => 'S.A.',
            '/sp[óo]łka\s+partnerska/iu' => 'S.P.',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $normalized = (string) preg_replace($pattern, $replacement, $normalized);
        }

        return preg_replace('/\s{2,}/', ' ', $normalized) ?? $normalized;
    }

    protected $fillable = [
        'name',
        'short_name',
        'nip',
        'city',
        'street',
        'postal_code',
        'description',
        'phone',
        'email',
        'logo',
        'client_id',
        'auditor_id',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function energyAudits(): HasMany
    {
        return $this->hasMany(EnergyAudit::class);
    }

    public function clientUsers(): HasMany
    {
        return $this->hasMany(User::class, 'company_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(CompanyContact::class, 'company_id');
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withTimestamps();
    }

    public function getNameAttribute($value): string
    {
        return self::normalizeLegalForm((string) $value);
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = self::normalizeLegalForm((string) $value);
    }
}
