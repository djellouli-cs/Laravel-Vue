<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Matricule extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'user_id',
    ];

    // One matricule can be used by many numeros
    public function numeros()
    {
        return $this->hasMany(Numero::class);
    }

    /**
     * Get the user who created/modified this matricule
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
