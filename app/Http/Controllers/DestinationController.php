<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Organisme;
use App\Models\Groupe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DestinationController extends Controller
{
    public function manageDestination()
    {
        return Inertia::render('Numero/MultiCrud/ManagerDestination', [
            'destinations' => Destination::with('organisme', 'groupes', 'user')->get(),
            'organismes' => Organisme::all(),
            'groupes' => Groupe::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_fr' => 'nullable|string|max:255',
            'organisme_id' => 'required|exists:organismes,id',
            'groupes' => 'nullable|array',
            'groupes.*' => 'exists:groupes,id',
        ]);

        $data = $request->only('name', 'name_fr', 'organisme_id');
        $data['user_id'] = auth()->id();
        $destination = Destination::create($data);

        if ($request->filled('groupes')) {
            $destination->groupes()->sync($request->groupes);
        }

        return redirect()->route('destination.manage')->with('success', 'Destination created.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_fr' => 'nullable|string|max:255',
            'organisme_id' => 'required|exists:organismes,id',
            'groupes' => 'nullable|array',
            'groupes.*' => 'exists:groupes,id',
        ]);

        $destination = Destination::findOrFail($id);
        $data = $request->only('name', 'name_fr', 'organisme_id');
        $data['user_id'] = auth()->id();
        $destination->update($data);

        if ($request->filled('groupes')) {
            $destination->groupes()->sync($request->groupes);
        }

        return redirect()->route('destination.manage')->with('success', 'Destination updated.');
    }

    public function destroy($id)
    {
        $destination = Destination::findOrFail($id);
        $destination->delete();

        return redirect()->route('destination.manage')->with('success', 'Destination deleted.');
    }
}
