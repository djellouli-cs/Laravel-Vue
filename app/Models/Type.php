<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Type extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
    ];

    // One Type has many Numeros
    public function numeros()
    {
        return $this->hasMany(Numero::class);
    }

    /**
     * Get the user who created/modified this type
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
