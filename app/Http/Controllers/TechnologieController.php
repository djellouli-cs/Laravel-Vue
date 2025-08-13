<?php

namespace App\Http\Controllers;

use App\Models\Technologie;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TechnologieController extends Controller
{
    public function technologies()
    {
        return Inertia::render('Numero/MultiCrud/Managertechnologies', [
            'technologies' => Technologie::all(),
        ]);
    }

    public function index()
    {
        return Technologie::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Technologie::create($request->only('name'));

        return back()->with('success', 'Technologie created.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $technologie = Technologie::findOrFail($id);
        $technologie->update($request->only('name'));

        return back()->with('success', 'Technologie updated.');
    }

    public function destroy($id)
    {
        $technologie = Technologie::findOrFail($id);
        $technologie->delete();

        return back()->with('success', 'Technologie deleted.');
    }
}
