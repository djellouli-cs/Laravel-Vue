<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'avatar',
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function isAdmin()
{
    return $this->role === 'admin';
}

/**
 * Get all numeros created by this user
 */
public function numeros()
{
    return $this->hasMany(Numero::class);
}

/**
 * Get all faxes created by this user
 */
public function faxes()
{
    return $this->hasMany(Fax::class);
}

/**
 * Get all organismes created by this user
 */
public function organismes()
{
    return $this->hasMany(Organisme::class);
}

/**
 * Get all destinations created by this user
 */
public function destinations()
{
    return $this->hasMany(Destination::class);
}

/**
 * Get all classes created by this user
 */
public function classes()
{
    return $this->hasMany(Classe::class);
}

/**
 * Get all types created by this user
 */
public function types()
{
    return $this->hasMany(Type::class);
}

/**
 * Get all reserves created by this user
 */
public function reserves()
{
    return $this->hasMany(Reserve::class);
}

/**
 * Get all technologies created by this user
 */
public function technologies()
{
    return $this->hasMany(Technologie::class);
}

/**
 * Get all factures created by this user
 */
public function factures()
{
    return $this->hasMany(Facture::class);
}

/**
 * Get all matricules created by this user
 */
public function matricules()
{
    return $this->hasMany(Matricule::class);
}

/**
 * Get all notes created by this user
 */
public function notes()
{
    return $this->hasMany(Note::class);
}

/**
 * Get all posts created by this user
 */
public function posts()
{
    return $this->hasMany(Post::class);
}

/**
 * Get all acheminements created by this user
 */
public function acheminements()
{
    return $this->hasMany(Acheminement::class);
}

/**
 * Get all services created by this user
 */
public function services()
{
    return $this->hasMany(Service::class);
}

}
