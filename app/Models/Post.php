<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'post',
        'marque',
        'numero_id', // foreign key to Numero
        'user_id',
    ];

    // A Post belongs to one Numero
    public function numero()
    {
        return $this->belongsTo(Numero::class);
    }

    /**
     * Get the user who created/modified this post
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
