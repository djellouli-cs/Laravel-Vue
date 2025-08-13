<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permanence extends Model
{
    use HasFactory;

    protected $fillable = [
        'DSemaine',
        'FSemaine',
        'PSemaine',
        'RSemaine',
    ];

    public function destinations()
{
    return $this->belongsToMany(Destination::class, 'destination_permanence')
                ->withPivot('ordre')
                ->withTimestamps();
}

    /**
     * Get the week status (MANTENANT, PROCHAIN, PRECEDANT)
     */
    public function getWeekStatusAttribute()
    {
        $today = now();
        $permanenceStart = \Carbon\Carbon::parse($this->DSemaine);
        $permanenceEnd = \Carbon\Carbon::parse($this->FSemaine);
        
        // Check if today falls within the permanence period
        if ($today->between($permanenceStart, $permanenceEnd)) {
            return 'MANTENANT';
        } elseif ($permanenceStart->gt($today)) {
            return 'PROCHAIN';
        } else {
            return 'PRECEDANT';
        }
    }

    /**
     * Get the week status with color class
     */
    public function getWeekStatusColorAttribute()
    {
        $status = $this->week_status;
        
        switch ($status) {
            case 'MANTENANT':
                return 'bg-green-100 text-green-800 border-green-200';
            case 'PROCHAIN':
                return 'bg-blue-100 text-blue-800 border-blue-200';
            case 'PRECEDANT':
                return 'bg-gray-100 text-gray-800 border-gray-200';
            default:
                return 'bg-gray-100 text-gray-800 border-gray-200';
        }
    }
}
