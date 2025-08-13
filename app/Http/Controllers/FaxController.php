<?php

namespace App\Http\Controllers;

use App\Models\Fax;
use App\Models\Numero;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaxController extends Controller
{
    public function index(Request $request)
    {
        $faxes = Fax::with('numero.destination.organisme', 'user')
            ->when($request->search, function ($query) use ($request) {
                $query->where('NDappel', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
            })
            ->orderBy('created_at', $request->sort === 'asc' ? 'asc' : 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Faxes/Index', [
            'faxes' => $faxes,
            'search' => $request->search,
            'sort' => $request->sort ?? 'desc'
        ]);
    }

    public function create()
    {
        // Get numeros that don't have faxes yet
        $availableNumeros = Numero::with('destination.organisme')
            ->whereDoesntHave('faxes')
            ->get();

        return Inertia::render('Faxes/Create', [
            'availableNumeros' => $availableNumeros
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'NDappel' => 'required|string|max:10|exists:numeros,NDappel',
            'description' => 'nullable|string|max:255',
        ]);

        // Check if fax already exists for this NDappel
        $existingFax = Fax::where('NDappel', $data['NDappel'])->first();
        if ($existingFax) {
            return back()->withErrors(['NDappel' => "Un fax existe déjà pour ce numéro d'appel ({$data['NDappel']})."]);
        }

        $data['user_id'] = auth()->id();
        $fax = Fax::create($data);

        return redirect()->route('faxes.index')
            ->with('success', 'Fax créé avec succès.');
    }

    public function show(Fax $fax)
    {
        $fax->load('numero.destination.organisme');
        return Inertia::render('Faxes/Show', [
            'fax' => $fax
        ]);
    }

    public function edit(Fax $fax)
    {
        $fax->load('numero.destination.organisme');
        
        // Get numeros that don't have faxes yet, plus the current one
        $availableNumeros = Numero::with('destination.organisme')
            ->where(function ($query) use ($fax) {
                $query->whereDoesntHave('faxes')
                      ->orWhere('NDappel', $fax->NDappel);
            })
            ->get();

        return Inertia::render('Faxes/Edit', [
            'fax' => $fax,
            'availableNumeros' => $availableNumeros
        ]);
    }

    public function update(Request $request, Fax $fax)
    {
        $data = $request->validate([
            'NDappel' => 'required|string|max:10|exists:numeros,NDappel',
            'description' => 'nullable|string|max:255',
        ]);

        // Check if fax already exists for this NDappel (excluding current fax)
        $existingFax = Fax::where('NDappel', $data['NDappel'])
            ->where('id', '!=', $fax->id)
            ->first();
        if ($existingFax) {
            return back()->withErrors(['NDappel' => "Un fax existe déjà pour ce numéro d'appel ({$data['NDappel']})."]);
        }

        $data['user_id'] = auth()->id();
        $fax->update($data);

        return redirect()->route('faxes.show', $fax)
            ->with('success', 'Fax mis à jour avec succès.');
    }

    public function destroy(Fax $fax)
    {
        $fax->delete();

        return redirect()->route('faxes.index')
            ->with('success', 'Fax supprimé avec succès.');
    }

    /**
     * Get fax statistics
     */
    public function statistics()
    {
        $totalNumeros = \App\Models\Numero::count();
        $totalFaxes = Fax::count();
        $faxPercentage = $totalNumeros > 0 ? round(($totalFaxes / $totalNumeros) * 100, 2) : 0;

        return Inertia::render('Faxes/Statistics', [
            'statistics' => [
                'total_numeros' => $totalNumeros,
                'total_faxes' => $totalFaxes,
                'fax_percentage' => $faxPercentage,
                'non_fax_numeros' => $totalNumeros - $totalFaxes
            ]
        ]);
    }
}
