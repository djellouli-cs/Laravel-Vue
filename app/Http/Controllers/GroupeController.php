<?php

namespace App\Http\Controllers;

use App\Models\Groupe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GroupeController extends Controller
{
    /**
     * Page Inertia : vue complète avec formulaire + tableau
     */
    public function manageGroupe()
    {
        return Inertia::render('Numero/MultiCrud/ManagerGroupe', [
            'groupes' => Groupe::with('destinations')->get(),
        ]);
    }

    /**
     * API : liste brute JSON (optionnel)
     */
    public function index()
    {
        return Groupe::all();
    }

    /**
     * Enregistrer un nouveau groupe
     */
    public function store(Request $request)
    {
        $request->validate([
            'groupes' => 'required|string|max:255',
        ]);

        Groupe::create($request->only('groupes'));

        return redirect()->route('groupe.manage')->with('success', 'Groupe créé avec succès.');
    }

    /**
     * Mettre à jour un groupe
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'groupes' => 'required|string|max:255',
        ]);

        $groupe = Groupe::findOrFail($id);
        $groupe->update($request->only('groupes'));

        return redirect()->route('groupe.manage')->with('success', 'Groupe mis à jour.');
    }

    /**
     * Supprimer un groupe
     */
    public function destroy($id)
    {
        $groupe = Groupe::findOrFail($id);
        $groupe->delete();

        return redirect()->route('groupe.manage')->with('success', 'Groupe supprimé.');
    }
} 