<?php

namespace App\Http\Controllers;

use App\Models\Acheminement;
use App\Models\Numero;
use App\Models\Technologie;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AcheminementController extends Controller
{
    // 🔹 Shared method for SWD / DIVERS pages /ADM
    private function renderView(string $view)
    {
        $acheminements = Acheminement::with([
            'numero',
            'numero.organisme',
            'numero.destination',
            'numero.fax',
            'numero.technologie',
        ])->get();

        $technologies = Technologie::all(['id', 'name']);

        return Inertia::render($view, [
            'acheminements' => $acheminements,
            'technologies' => $technologies
        ]);
    }

    // 🟢 SWD View
    public function swd()
    {
        return $this->renderView('Autocom/SWD');
    }

    // 🟡 DIVERS View
    public function divers()
    {
        return $this->renderView('Autocom/DIVERS');
    }
// 🟡 ADM View
    public function adm()
    {
        return $this->renderView('Autocom/ADM');
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
        $data = $request->validate([
            'numero_id' => 'required|exists:numeros,id',
            'acheminement' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        Acheminement::create($data);

        return redirect()->route('acheminement.manage')
            ->with('success', 'Acheminement created.');
    }

    // 🟢 Update an existing acheminement
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'numero_id' => 'required|exists:numeros,id',
            'acheminement' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $acheminement = Acheminement::findOrFail($id);
        $acheminement->update($data);

        return redirect()->route('acheminement.manage')
            ->with('success', 'Acheminement updated.');
    }

    // 🟢 Delete an acheminement
    public function destroy($id)
    {
        $acheminement = Acheminement::findOrFail($id);
        $acheminement->delete();

        return redirect()->route('acheminement.manage')
            ->with('success', 'Acheminement deleted.');
    }
}
