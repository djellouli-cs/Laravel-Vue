<?php

namespace App\Http\Controllers;
use App\Models\Permanence;
use App\Models\Destination;
use App\Models\Groupe;
use App\Models\Numero;
use Inertia\Inertia;
use Illuminate\Http\Request;

class StandardController extends Controller
{
    /**
     * Display all numeros for the standard page.
     */
    public function index()
    {
        $numeros = Numero::with([
            'organisme',
            'destination.groupes',
            'service',
            'classe',
            'type',
            'reserve',
            'technologie',
            'facture',
            'matricule',
            'acheminements',
            'notes',
            'fax',
            'post',
        ])
        // ✅ Hide numeros with related type.name = 'PRIVEE1'
        ->whereHas('type', function ($query) {
            $query->where('name', '!=', 'PRIVEE1');
        })
        ->orderBy('technologie_id')
        ->orderBy('matricule_id')
        ->get();
///permanence of week 
 $today = now()->format('Y-m-d');
        \Log::info('Today is: ' . $today);

        // Find permanence for today
        $thisWeekPermanence = Permanence::where('DSemaine', '<=', $today)
            ->where('FSemaine', '>=', $today)
            ->with('destinations.numeros.organisme')
            ->first();

        \Log::info('Found permanence:', $thisWeekPermanence ? $thisWeekPermanence->toArray() : []);

        // Get all destinations with their numeros for the helper functions (like in index)
        $destinations = Destination::with('numeros.technologie')->get();

        return Inertia::render('Standard/Index', [
            'numeros' => $numeros,
            'permanence' => $thisWeekPermanence,
            
            'destinations' => $destinations

        ]);
    }

    /**
     * Show a single numero by NDappel (if you need it).
     */
    public function show(Request $request)
    {
        $ndappel = $request->query('ndappel');

        $numero = Numero::where('NDappel', $ndappel)
            ->with([
                'organisme',
                'destination.groupes',
                'service',
                'classe',
                'type',
                'reserve',
                'technologie',
                'facture',
                'matricule',
                'acheminements',
                'notes',
                'fax',
                'post',
            ])
            ->firstOrFail();

        return Inertia::render('Standard/Show', [
            'numero' => $numero,
        ]);
    }

    /**
     * Update NDappel (only if technologie is MOBILE).
     */
    public function updateNDappel(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:numeros,id',
            'NDappel' => 'required|string|max:255',
        ]);

        $numero = Numero::find($validated['id']);

        // ✅ Update only if technologie is MOBILE
        if (strtoupper($numero->technologie->name ?? '') === 'MOBILE') {
            $numero->NDappel = $validated['NDappel'];
            $numero->save();
            $numero->load([
            'organisme',
            'destination',
            'classe',
            'type',
            'service',
            'acheminements'
        ]);

        // Broadcast ONCE
        broadcast(new NumeroUpdated($numero))->toOthers();
        
        }

        return back();
    }
    

}
