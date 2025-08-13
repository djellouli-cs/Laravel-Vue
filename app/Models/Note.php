<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',      // or whatever your note stores
        'numero_id',
        'user_id',
    ];

    // A note belongs to one numero
    public function numero()
    {
        return $this->belongsTo(Numero::class);
    }

    /**
     * Get the user who created/modified this note
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
