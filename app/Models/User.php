<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'avatar',
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Append custom attributes to JSON.
     */
    protected $appends = ['avatar_url'];

    /**
     * ✅ Avatar accessor
     * - If avatar exists → /storage/avatars/xxx.png
     * - If NULL → default avatar
     */
    public function getAvatarUrlAttribute()
    {
        return $this->avatar
            ? Storage::url($this->avatar)
            : asset('images/default-avatar.png');
    }

    /**
     * Roles
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Relations
     */
    public function numeros()
    {
        return $this->hasMany(Numero::class);
    }

    public function faxes()
    {
        return $this->hasMany(Fax::class);
    }

    public function organismes()
    {
        return $this->hasMany(Organisme::class);
    }

    public function destinations()
    {
        return $this->hasMany(Destination::class);
    }

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }

    public function types()
    {
        return $this->hasMany(Type::class);
    }

    public function reserves()
    {
        return $this->hasMany(Reserve::class);
    }

    public function technologies()
    {
        return $this->hasMany(Technologie::class);
    }

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    public function matricules()
    {
        return $this->hasMany(Matricule::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function acheminements()
    {
        return $this->hasMany(Acheminement::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
