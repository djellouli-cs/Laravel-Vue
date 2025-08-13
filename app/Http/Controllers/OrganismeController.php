<?php

namespace App\Http\Controllers;

use App\Models\Organisme;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrganismeController extends Controller
{
    /**
     * Page Inertia : vue complète avec formulaire + tableau
     */
    public function manageOrganisme()
    {
        return Inertia::render('Numero/MultiCrud/ManagerOrganisme', [
            'organismes' => Organisme::with('user')->get(),
        ]);
    }

    /**
     * API : liste brute JSON (optionnel)
     */
    public function index()
    {
        return Organisme::all();
    }

    /**
     * Enregistrer un nouvel organisme
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_fr' => 'nullable|string|max:255',
        ]);

        $data = $request->only('name', 'name_fr');
        $data['user_id'] = auth()->id();
        Organisme::create($data);

        return redirect()->route('organisme.manage')->with('success', 'Organisme créé avec succès.');
    }

    /**
     * Mettre à jour un organisme
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_fr' => 'nullable|string|max:255',
        ]);

        $organisme = Organisme::findOrFail($id);
        $data = $request->only('name', 'name_fr');
        $data['user_id'] = auth()->id();
        $organisme->update($data);

        return redirect()->route('organisme.manage')->with('success', 'Organisme mis à jour.');
    }

    /**
     * Supprimer un organisme
     */
    public function destroy($id)
    {
        $organisme = Organisme::findOrFail($id);
        $organisme->delete();

        return redirect()->route('organisme.manage')->with('success', 'Organisme supprimé.');
    }
}
