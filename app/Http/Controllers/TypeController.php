<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TypeController extends Controller
{
    public function manageType()
    {
        return Inertia::render('Numero/MultiCrud/ManagerType', [
            'types' => Type::all(),
        ]);
    }

    public function index()
    {
        return Type::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Type::create($request->only('name'));

        return redirect()->route('type.manage')->with('success', 'Type created.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $type = Type::findOrFail($id);
        $type->update($request->only('name'));

        return redirect()->route('type.manage')->with('success', 'Type updated.');
    }

    public function destroy($id)
    {
        $type = Type::findOrFail($id);
        $type->delete();

        return redirect()->route('type.manage')->with('success', 'Type deleted.');
    }
}
