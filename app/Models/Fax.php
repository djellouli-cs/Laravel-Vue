<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fax extends Model
{
    use HasFactory;

    protected $fillable = [
        'NDappel',
        'description',
        'user_id',
    ];


 
    /**
     * Get all numeros that can have this fax number
     */
    public function numero()
    {
        return $this->belongsTo(Numero::class, 'NDappel', 'NDappel');
    }

    /**
     * Get the user who created/modified this fax
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
