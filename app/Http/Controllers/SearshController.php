<?php

namespace App\Http\Controllers;

use App\Models\Permanence;
use App\Models\Destination;
use App\Models\Numero;
use Inertia\Inertia;
use Illuminate\Http\Request;

class SearshController extends Controller
{
    /**
     * Display all numeros for the search page.
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
        
        ->orderBy('technologie_id')
        ->orderBy('matricule_id')
        ->get();

        // Permanence of the week
        $today = now()->format('Y-m-d');

        $thisWeekPermanence = Permanence::where('DSemaine', '<=', $today)
            ->where('FSemaine', '>=', $today)
            ->with('destinations.numeros.organisme')
            ->first();

        // Get all destinations with their numeros
        $destinations = Destination::with('numeros.technologie')->get();

        return Inertia::render('Searsh', [
            'numeros' => $numeros,
            'permanence' => $thisWeekPermanence,
            'destinations' => $destinations,
        ]);
    }

    /**
     * Show a single numero by NDappel.
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

        return Inertia::render('Searsh', [
            'numero' => $numero,
        ]);
    }

    /**
     * Update NDappel (only if technologie is MOBILE or ALGERIE TELECOM + organisme = الولايات).
     */
    public function updateNDappel(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:numeros,id',
            'NDappel' => 'required|string|max:255',
        ]);

        $numero = Numero::findOrFail($validated['id']);

        $techName = strtoupper($numero->technologie->name ?? '');
        $orgName  = $numero->organisme->name ?? '';

        // ✅ Correct condition
        if ($techName === 'MOBILE' || ($techName === 'ALGERIE TELECOM' && $orgName === 'الولايات')) {
            $numero->NDappel = $validated['NDappel'];
            $numero->save();
        }

        return back();
    }
}
