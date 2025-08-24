<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Plage;

class IpAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'ipAdresses',
        'some_other_fields', // Replace with your actual columns
    ];

    public function plage()
    {
        return $this->belongsTo(Plage::class, 'ipAdresses', 'ipAdresses');
    }
}
