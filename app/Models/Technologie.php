<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Technologie extends Model
{
     use HasFactory;

    protected $fillable = ['name', 'user_id'];

    // One Technologie has many Numeros
    public function numeros()
    {
        return $this->hasMany(Numero::class);
    }

    /**
     * Get the user who created/modified this technologie
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
