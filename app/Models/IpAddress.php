<?php

namespace App\Models;
use App\Models\plage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpAddress extends Model
{
    use HasFactory;

    // Add the fields you want to be mass-assignable
    protected $fillable = [
        'ipAdresses',  // Add the 'ipAdresses' field here
        'organisme',
        'destination',
        'Application',
        'port',
        'mask',
        'note',
    ];

    // Optionally, if you want to use timestamps
    public $timestamps = true;
public function plage()
    {
        return $this->belongsTo(plage::class,'ipAdresses','ipAdresses');
    }

}
