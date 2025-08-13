<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classe extends Model
{
    use HasFactory;

   protected $fillable = ['classe', 'user_id'];

    // One Classe has many Numeros
    public function numeros()
    {
        return $this->hasMany(Numero::class);
    }

    /**
     * Get the user who created/modified this classe
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
