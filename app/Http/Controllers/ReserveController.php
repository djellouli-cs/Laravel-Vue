<?php

namespace App\Http\Controllers;

use App\Models\Reserve;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReserveController extends Controller
{
    // Display the reserve management view
    public function manageReserve()
    {
        return Inertia::render('Numero/MultiCrud/ManagerReserve', [
            'reserves' => Reserve::all(),
        ]);
    }

    // Retrieve all reserves
    public function index()
    {
        return Reserve::all();
    }

    // Store a new reserve
    public function store(Request $request)
    {
        $request->validate([
            'is_reserved' => 'required|boolean',
            'reserve' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->is_reserved) {
            Reserve::create([
                'reserve' => $request->reserve, // can be null
                'is_reserved' => true,
            ]);

            return redirect()->route('reserve.manage')->with('success', 'Reserve created.');
        }

        return redirect()->route('reserve.manage')->with('success', 'Not saved. is_reserved was false.');
    }

    // Update an existing reserve
    public function update(Request $request, $id)
    {
        $request->validate([
            'is_reserved' => 'required|boolean',
            'reserve' => ['nullable', 'string', 'max:255'],
        ]);

        $reserve = Reserve::findOrFail($id);
        $reserve->reserve = $request->is_reserved ? $request->reserve : null;
        $reserve->is_reserved = $request->is_reserved;
        $reserve->save();

        return redirect()->route('reserve.manage')->with('success', 'Reserve updated.');
    }

    // Delete a reserve
    public function destroy($id)
    {
        $reserve = Reserve::findOrFail($id);
        $reserve->delete();

        return redirect()->route('reserve.manage')->with('success', 'Reserve deleted.');
    }
}
