<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnergyAuditMasterData extends Model
{
    use HasFactory;

    protected $table = 'energy_audit_master_data';

    protected $fillable = [
        'company_id',
        'form_data',
        'completion_percent',
        'last_saved_at',
    ];

    protected $casts = [
        'form_data'         => 'array',
        'last_saved_at'     => 'datetime',
        'completion_percent' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get a specific field value by its data-id.
     */
    public function field(string $fieldId): mixed
    {
        return data_get($this->form_data, $fieldId);
    }

    /**
     * Get form data as flat array with defaults, safe for JSON output.
     */
    public function getFormDataSafe(): array
    {
        return is_array($this->form_data) ? $this->form_data : [];
    }
}
