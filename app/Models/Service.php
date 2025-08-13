<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_fr', 'user_id'];

    public function numeros()
    {
        return $this->hasMany(Numero::class);
    }

    /**
     * Get the user who created/modified this service
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
