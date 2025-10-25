<?php

namespace App\Http\Controllers;

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
        ->orderBy('technologie_id')     // ✅ Order by technologie
        ->orderBy('matricule_id')       // ✅ Then by matricule first
        ->get();

        return Inertia::render('Standard/Index', [
            'numeros' => $numeros,
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
    public function updateNDappel(Request $request)
{
    $validated = $request->validate([
        'id' => 'required|exists:numeros,id',
        'NDappel' => 'required|string|max:255',
    ]);

    $numero = Numero::find($validated['id']);

    // Update only if technologie is MOBILE
    if (strtoupper($numero->technologie->name ?? '') === 'MOBILE') {
        $numero->NDappel = $validated['NDappel'];
        $numero->save();
    }

    return back();
}

}
