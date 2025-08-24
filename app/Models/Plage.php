<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\IpAddress;

class Plage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ipAdresses',
        'direction',
        // Add other fillable fields if needed
    ];

    public function ipaddresses()
    {
        return $this->hasMany(IpAddress::class, 'ipAdresses', 'ipAdresses');
    }
}
