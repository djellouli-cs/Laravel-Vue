<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_fr',
        'organisme_id',
        'user_id',
    ];

    // 🔁 Chaque destination appartient à un organisme
    public function organisme()
    {
        return $this->belongsTo(Organisme::class);
    }

    // 🔁 Une destination peut avoir plusieurs numéros
    public function numeros()
    {
        return $this->hasMany(Numero::class);
    }

    // 🔁 Une destination peut appartenir à plusieurs groupes
    public function groupes()
    {
        return $this->belongsToMany(Groupe::class, 'destination_groupe');
    }
    public function permanences()
{
    return $this->belongsToMany(Permanence::class, 'destination_permanence')
                ->withPivot('ordre')
                ->withTimestamps();
}

/**
 * Get the user who created/modified this destination
 */
public function user()
{
    return $this->belongsTo(User::class);
}

}
