<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Numero;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoteController extends Controller
{
    // Show note management page
    public function manageNote()
    {
        return Inertia::render('Numero/MultiCrud/ManagerNote', [
            'notes' => Note::with('numero')->get(),
              'numeros' => Numero::all(),

        ]);
    }

    // Fetch all notes (API style, optional)
    public function index()
    {
        return Note::with('numero')->get();
    }

    // Store a new note
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'numero_id' => 'required|exists:numeros,id',
        ]);

        Note::create($request->only('content', 'numero_id'));

        return redirect()->route('note.manage')->with('success', 'Note created.');
    }

    // Update an existing note
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'numero_id' => 'required|exists:numeros,id',
        ]);

        $note = Note::findOrFail($id);
        $note->update($request->only('content', 'numero_id'));

        return redirect()->route('note.manage')->with('success', 'Note updated.');
    }

    // Delete a note
    public function destroy($id)
    {
        $note = Note::findOrFail($id);
        $note->delete();

        return redirect()->route('note.manage')->with('success', 'Note deleted.');
    }
}
