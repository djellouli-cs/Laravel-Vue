<?php

namespace App\Http\Controllers;

use App\Models\Numero;
use App\Models\Organisme;
use App\Models\Destination;
use App\Models\Service;
use App\Models\Technologie;
use App\Models\Groupe;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AnnuaireController extends Controller
{
    /**
     * Show a numero by its NDappel query param.
     */
    public function show(Request $request)
    {
        // Retrieve ?ndappel=xxxx from the URL
        $ndappel = $request->query('ndappel');

        // Find the numero by NDappel column
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
            ->firstOrFail(); // throw 404 if not found

        return Inertia::render('Annuaire/Show', [
            'numero' => $numero,
        ]);
    }

    /**
     * List all numeros.
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
        ])->get();

        return Inertia::render('Annuaire/Index', [
            'numeros' => $numeros,
        ]);
    }

    /**
     * Filter page.
     */
    public function filter()
    {
        $numeros = Numero::with([
            'organisme',
            'destination.groupes',
            'service',
            'technologie',
            'matricule',
        ])->get();

        return Inertia::render('Annuaire/Filter', [
            'numeros'      => $numeros,
            'organismes'   => Organisme::all(),
            'destinations' => Destination::all(),
            'services'     => Service::all(),
            'technologies' => Technologie::all(),
            'groupes'      => Groupe::all(),
        ]);
    }

    /**
     * Recherche page.
     */
    public function recherche()
    {
        $numeros = Numero::with([
            'organisme',
            'destination.groupes',
            'service',
            'technologie',
            'matricule',
        ])->get();

        return Inertia::render('Annuaire/Recherche', [
            'numeros'      => $numeros,
            'organismes'   => Organisme::all(),
            'destinations' => Destination::all(),
            'services'     => Service::all(),
            'technologies' => Technologie::all(),
            'groupes'      => Groupe::all(),
        ]);
    }
}
