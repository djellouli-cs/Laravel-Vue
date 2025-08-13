<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Numero extends Model
{
    use HasFactory;

   protected $fillable = [
    'NDappel',
    'Position',
    'destination_id',
    'classe_id',
    'type_id',
    'reserve_id',
    'technologie_id',
    'facture_id',
    'matricule_id',
    'organisme_id',
    'service_id',
    'user_id',
];


    // 🔗 Relationships

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function reserve()
    {
        return $this->belongsTo(Reserve::class);
    }

    public function technologie()
    {
        return $this->belongsTo(Technologie::class);
    }

    public function facture()
    {
        return $this->belongsTo(Facture::class);
    }

    public function matricule()
    {
        return $this->belongsTo(Matricule::class);
    }

    public function acheminements()
    {
        return $this->hasMany(Acheminement::class);
    }

    public function post()
{
    return $this->hasOne(Post::class);
}


    public function notes()
    {
        return $this->hasMany(Note::class);
    }
    public function organisme()
{
    return $this->belongsTo(\App\Models\Organisme::class);
}

public function service()
{
    return $this->belongsTo(Service::class);
}

/**
 * Get the fax associated with this numero through NDappel
 */
public function fax()
{
    return $this->hasOne(Fax::class, 'NDappel', 'NDappel');
}

/**
 * Get all faxes that can be associated with this numero
 */
public function faxes()
{
    return $this->hasMany(Fax::class, 'NDappel', 'NDappel');
}

/**
 * Get the user who created/modified this numero
 */
public function user()
{
    return $this->belongsTo(User::class);
}
}
