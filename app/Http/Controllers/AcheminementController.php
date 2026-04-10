<?php

namespace App\Http\Controllers;

use App\Models\Acheminement;
use App\Models\Numero;
use App\Models\Technologie;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\NumeroUpdated;

class AcheminementController extends Controller
{
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

    public function swd()
    {
        return $this->renderView('Autocom/SWD');
    }

    public function divers()
    {
        return $this->renderView('Autocom/DIVERS');
    }

    public function adm()
    {
        return $this->renderView('Autocom/ADM');
    }

    public function ptt2er112()
    {
        return $this->renderView('Autocom/PTT-2-112');
    }

    public function manageAcheminement()
    {
        return Inertia::render('Numero/MultiCrud/ManagerAcheminement', [
            'acheminements' => Acheminement::with('numero')->get(),
            'numeros' => Numero::all(),
        ]);
    }

    /* =========================
       CREATE
    ========================= */
    public function store(Request $request)
    {
        $data = $request->validate([
            'numero_id' => 'required|exists:numeros,id',
            'acheminement' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $acheminement = Acheminement::create($data);

        // 🔥 reload numero with relations
        $numero = Numero::with([
            'acheminements',
            'organisme',
            'destination',
            'fax',
            'technologie'
        ])->find($acheminement->numero_id);

        broadcast(new NumeroUpdated($numero))->toOthers();

        return redirect()->route('acheminement.manage')
            ->with('success', 'Acheminement created.');
    }

    /* =========================
       UPDATE
    ========================= */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'numero_id' => 'required|exists:numeros,id',
            'acheminement' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $acheminement = Acheminement::findOrFail($id);
        $acheminement->update($data);

        $numero = Numero::with([
            'acheminements',
            'organisme',
            'destination',
            'fax',
            'technologie'
        ])->find($acheminement->numero_id);

        broadcast(new NumeroUpdated($numero))->toOthers();

        return redirect()->route('acheminement.manage')
            ->with('success', 'Acheminement updated.');
    }

    /* =========================
       DELETE
    ========================= */
    public function destroy($id)
    {
        $acheminement = Acheminement::findOrFail($id);

        $numeroId = $acheminement->numero_id;

        $acheminement->delete();

        $numero = Numero::with([
            'acheminements',
            'organisme',
            'destination',
            'fax',
            'technologie'
        ])->find($numeroId);

        broadcast(new NumeroUpdated($numero))->toOthers();

        return redirect()->route('acheminement.manage')
            ->with('success', 'Acheminement deleted.');
    }
}