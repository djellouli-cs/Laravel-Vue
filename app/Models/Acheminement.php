<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Acheminement extends Model
{
    use HasFactory;

    protected $fillable = [
    'numero_id',
    'acheminement',
    'description',
    'user_id',
];


    /**
     * Get the numero associated with this acheminement.
     */
    public function numero()
    {
        return $this->belongsTo(Numero::class);
    }

    /**
     * Get the user who created/modified this acheminement
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
