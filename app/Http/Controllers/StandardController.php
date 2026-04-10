<?php

namespace App\Http\Controllers;

use App\Models\Permanence;
use App\Models\Numero;
use App\Models\Destination;
use App\Models\Classe;
use App\Models\Type;
use App\Models\Reserve;
use App\Models\Technologie;
use App\Models\Facture;
use App\Models\Matricule;
use App\Models\Organisme;
use App\Models\Acheminement;
use App\Models\Service;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Events\NumeroUpdated;

class StandardController extends Controller
{
    /**
     * Shared relations (IMPORTANT: must match other controllers)
     */
    private function relations()
    {
        return [
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
        ];
    }

    /**
     * Display all numeros
     */
    public function index()
    {
        $numeros = Numero::with($this->relations())
            ->whereHas('type', function ($q) {
                $q->where('name', '!=', 'PRIVEE1');
            })
            ->orderBy('technologie_id')
            ->orderBy('matricule_id')
            ->get();

        $today = now()->toDateString();

        $permanence = Permanence::where('DSemaine', '<=', $today)
            ->where('FSemaine', '>=', $today)
            ->with('destinations.numeros.organisme')
            ->first();

        $destinations = Destination::with('numeros.technologie')->get();

        return Inertia::render('Standard/Index', [
            'numeros' => $numeros,
            'permanence' => $permanence,
            'destinations' => $destinations,
        ]);
    }

    /**
     * Show one numero
     */
    public function show(Request $request)
    {
        $numero = Numero::with($this->relations())
            ->where('NDappel', $request->query('ndappel'))
            ->firstOrFail();

        return Inertia::render('Standard/Show', [
            'numero' => $numero,
        ]);
    }

    /**
     * Update NDappel + broadcast
     */
    public function updateNDappel(Request $request)
{
    $validated = $request->validate([
        'id' => 'required|exists:numeros,id',
        'NDappel' => 'required|string|max:255',
    ]);

    $numero = Numero::with($this->relations())
        ->findOrFail($validated['id']);

    if (strtoupper($numero->technologie->name ?? '') !== 'MOBILE') {
        return back()->with('error', 'Update not allowed for this technologie');
    }

    $numero->NDappel = $validated['NDappel'];
    $numero->save();

    // ❌ REMOVE refresh/load (not needed)
    // $numero->refresh();
    // $numero->load($this->relations());

    \Log::info('Standard update NDappel', [
        'id' => $numero->id,
        'NDappel' => $numero->NDappel,
    ]);

    broadcast(new NumeroUpdated($numero))->toOthers();
    
\Log::info('BEFORE BROADCAST', ['id' => $numero->id]);
    return back()->with('success', 'NDappel updated successfully');
}
}