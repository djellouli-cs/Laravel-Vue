<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClasseController extends Controller
{
    public function manageClasse()
    {
        return Inertia::render('Numero/MultiCrud/ManagerClasse', [
            'classes' => Classe::with('user')->get(),
        ]);
    }

    public function index()
    {
        return Classe::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'classe' => 'required|string|max:255',
        ]);

        $data = $request->only('classe');
        $data['user_id'] = auth()->id();
        Classe::create($data);

        return redirect()->route('classe.manage')->with('success', 'Classe created.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'classe' => 'required|string|max:255',
        ]);

        $classe = Classe::findOrFail($id);
        $data = $request->only('classe');
        $data['user_id'] = auth()->id();
        $classe->update($data);

        return redirect()->route('classe.manage')->with('success', 'Classe updated.');
    }

    public function destroy($id)
    {
        $classe = Classe::findOrFail($id);
        $classe->delete();

        return redirect()->route('classe.manage')->with('success', 'Classe deleted.');
    }
}
