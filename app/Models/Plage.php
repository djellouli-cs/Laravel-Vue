<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\IpAddress;

class Plage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ipAdresses', // ⚠️ this might not be needed anymore if you are now using plage_id
        'direction',
    ];

    /**
     * Relation: One Plage has many IpAddresses
     */
    public function ipAddresses()
    {
        return $this->hasMany(IpAddress::class, 'ipAdresses', 'ipAdresses');
        
    }
}
