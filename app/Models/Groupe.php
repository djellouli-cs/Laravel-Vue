<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    protected $table = 'groupes';

    protected $fillable = ['groupes'];

    public $timestamps = true;

    // 🔁 Un groupe peut contenir plusieurs destinations
    public function destinations()
    {
        return $this->belongsToMany(Destination::class, 'destination_groupe');
    }
}
