<?php

namespace App\Http\Controllers;

use App\Models\IpAddress;
use App\Models\Plage;
use Illuminate\Http\Request;
use Inertia\Inertia;
   use App\Models\Numero;

   
class AnnuaireController extends Controller
{

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
        'post'

    ])->get();

    return inertia('Annuaire/Index', [
        'numeros' => $numeros,
    ]);
}

public function filter()
{
    $numeros = \App\Models\Numero::with(['organisme', 'destination.groupes', 'service', 'technologie', 'matricule'])->get();
    $organismes = \App\Models\Organisme::all();
    $destinations = \App\Models\Destination::all();
    $services = \App\Models\Service::all();
    $technologies = \App\Models\Technologie::all();
    $groupes = \App\Models\Groupe::all();
    return inertia('Annuaire/Filter', [
        'numeros' => $numeros,
        'organismes' => $organismes,
        'destinations' => $destinations,
        'services' => $services,
        'technologies' => $technologies,
        'groupes' => $groupes,
    ]);
}

public function recherche()
{
    $numeros = \App\Models\Numero::with(['organisme', 'destination.groupes', 'service', 'technologie', 'matricule'])->get();
    $organismes = \App\Models\Organisme::all();
    $destinations = \App\Models\Destination::all();
    $services = \App\Models\Service::all();
    $technologies = \App\Models\Technologie::all();
    $groupes = \App\Models\Groupe::all();
    return inertia('Annuaire/Recherche', [
        'numeros' => $numeros,
        'organismes' => $organismes,
        'destinations' => $destinations,
        'services' => $services,
        'technologies' => $technologies,
        'groupes' => $groupes,
    ]);
}

}
