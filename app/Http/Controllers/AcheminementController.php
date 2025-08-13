<?php

namespace App\Http\Controllers;

use App\Models\Acheminement;
use App\Models\Numero;
use App\Models\Technologie;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AcheminementController extends Controller
{
    // 🟢 SWD View
   public function swd()
{
    $acheminements = Acheminement::with([
        'numero',
        'numero.organisme',
        'numero.destination',
        'numero.fax',
        'numero.technologie',
    ])->get();

    $technologies = Technologie::all(['id', 'name']); // send list to Vue

    return Inertia::render('Autocom/SWD', [
        'acheminements' => $acheminements,
        'technologies' => $technologies
    ]);
}


    // 🟢 API index
    public function index()
    {
        $acheminements = Acheminement::with([
            'numero',
            'numero.organisme',
            'numero.destination',
        ])->get();

        return response()->json($acheminements);
    }

    // 🟢 Manager page
    public function manageAcheminement()
    {
        return Inertia::render('Numero/MultiCrud/ManagerAcheminement', [
            'acheminements' => Acheminement::with('numero')->get(),
            'numeros' => Numero::all(),
        ]);
    }

    // 🟢 Store a new acheminement
    public function store(Request $request)
    {
        $request->validate([
            'numero_id' => 'required|exists:numeros,id',
            'acheminement' => 'required|string|max:255',
            'description' => 'nullable|string|max:255', // ✅ Added description
        ]);

        Acheminement::create($request->all());

        return redirect()->route('acheminement.manage')->with('success', 'Acheminement created.');
    }

    // 🟢 Update an existing acheminement
    public function update(Request $request, $id)
    {
        $request->validate([
            'numero_id' => 'required|exists:numeros,id',
            'acheminement' => 'required|string|max:255',
            'description' => 'nullable|string|max:255', // ✅ Added description
        ]);

        $acheminement = Acheminement::findOrFail($id);
        $acheminement->update($request->all());

        return redirect()->route('acheminement.manage')->with('success', 'Acheminement updated.');
    }

    // 🟢 Delete an acheminement
    public function destroy($id)
    {
        $acheminement = Acheminement::findOrFail($id);
        $acheminement->delete();

        return redirect()->route('acheminement.manage')->with('success', 'Acheminement deleted.');
    }
}
