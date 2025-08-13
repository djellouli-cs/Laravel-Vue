<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organisme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_fr', // assuming you added French name
        'user_id',
    ];

    // One organisme has many destinations
    public function destinations()
    {
        return $this->hasMany(Destination::class);
    }

    /**
     * Get the user who created/modified this organisme
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
