<?php

namespace App\Http\Controllers;

use App\Models\Matricule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MatriculeController extends Controller
{
    public function manageMatricule()
    {
        return Inertia::render('Numero/MultiCrud/ManagerMatricule', [
            'matricules' => Matricule::all(),
        ]);
    }

    public function index()
    {
        return Matricule::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'matricule' => 'required|string|max:255',
        ]);

        Matricule::create($request->only('matricule'));

        return redirect()->route('matricule.manage')->with('success', 'Matricule created.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'matricule' => 'required|string|max:255',
        ]);

        $matricule = Matricule::findOrFail($id);
        $matricule->update($request->only('matricule'));

        return redirect()->route('matricule.manage')->with('success', 'Matricule updated.');
    }

    public function destroy($id)
    {
        $matricule = Matricule::findOrFail($id);
        $matricule->delete();

        return redirect()->route('matricule.manage')->with('success', 'Matricule deleted.');
    }
}
