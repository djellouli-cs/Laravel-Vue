<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'ipAdresses',
        'destination',
        'organisme',
        'Application',
        'port',
        'mask',
        'note',
    ];

    public function plage()
    {
        return $this->belongsTo(Plage::class, 'ipAdresses', 'ipAdresses');
    }
}
