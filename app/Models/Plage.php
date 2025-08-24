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
        // Other fillable fields
    ];

    public function ipaddresses()
    {
        return $this->belongsTo(IpAddress::class, 'ipAdresses', 'ipAdresses');
    }
}
