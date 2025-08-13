<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reserve extends Model
{
    use HasFactory;

    // Fields that are mass assignable
    protected $fillable = [
        'reserve',  // This is likely a name or description of the reserve
        'is_reserved',  // This should be the boolean flag to check if it's reserved
        'user_id',
    ];

    // Cast the 'is_reserved' attribute to a boolean
    protected $casts = [
        'is_reserved' => 'boolean',  // This ensures 'is_reserved' is treated as a boolean
    ];

    // One reserve has many 'numeros' (relations with other models)
    public function numeros()
    {
        return $this->hasMany(Numero::class);
    }

    /**
     * Get the user who created/modified this reserve
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
