<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'facture',
        'is_factured',
        'user_id',
        // Provider fields for regular factures
        'provider',
        'plan',
        'monthly_cost',
        'start_date',
        'end_date',
        'notes',
        // ADSL fields
        'adsl_provider',
        'adsl_plan',
        'adsl_monthly_cost',
        'adsl_start_date',
        'adsl_end_date',
        'is_adsl',
        'adsl_notes',
    ];

    // Optionally cast to boolean
    protected $casts = [
        'is_factured' => 'boolean',
        'is_adsl' => 'boolean',
        'monthly_cost' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'adsl_monthly_cost' => 'decimal:2',
        'adsl_start_date' => 'date',
        'adsl_end_date' => 'date',
    ];

    // One facture can be linked to many numeros
    public function numeros()
    {
        return $this->hasMany(Numero::class);
    }

    /**
     * Get the user who created/modified this facture
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only ADSL factures
     */
    public function scopeAdsl($query)
    {
        return $query->where('is_adsl', true);
    }

    /**
     * Scope to get only non-ADSL factures
     */
    public function scopeNonAdsl($query)
    {
        return $query->where('is_adsl', false);
    }

    /**
     * Check if this facture is for ADSL service
     */
    public function isAdslService()
    {
        return $this->is_adsl;
    }

    /**
     * Get the total cost for ADSL service (monthly cost * duration)
     */
    public function getAdslTotalCost()
    {
        if (!$this->is_adsl || !$this->adsl_monthly_cost) {
            return 0;
        }

        if (!$this->adsl_start_date || !$this->adsl_end_date) {
            return $this->adsl_monthly_cost;
        }

        $months = $this->adsl_start_date->diffInMonths($this->adsl_end_date);
        return $this->adsl_monthly_cost * $months;
    }
}
