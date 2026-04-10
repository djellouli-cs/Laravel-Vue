<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Numero;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\NumeroUpdated;

class NoteController extends Controller
{
    public function manageNote()
    {
        return Inertia::render('Numero/MultiCrud/ManagerNote', [
            'notes' => Note::with('numero')->get(),
            'numeros' => Numero::with('notes')->get(),
        ]);
    }

    public function index()
    {
        return Note::with('numero')->get();
    }

    /* =========================
       CREATE
    ========================= */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'numero_id' => 'required|exists:numeros,id',
        ]);

        $note = Note::create($request->only('content', 'numero_id'));

        // 🔥 reload numero with notes
        $numero = Numero::with('notes')->find($note->numero_id);

        broadcast(new NumeroUpdated($numero))->toOthers();

        return redirect()->route('note.manage')->with('success', 'Note created.');
    }

    /* =========================
       UPDATE
    ========================= */
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'numero_id' => 'required|exists:numeros,id',
        ]);

        $note = Note::findOrFail($id);
        $note->update($request->only('content', 'numero_id'));

        $numero = Numero::with('notes')->find($note->numero_id);

        broadcast(new NumeroUpdated($numero))->toOthers();

        return redirect()->route('note.manage')->with('success', 'Note updated.');
    }

    /* =========================
       DELETE
    ========================= */
    public function destroy($id)
    {
        $note = Note::findOrFail($id);

        $numeroId = $note->numero_id;

        $note->delete();

        $numero = Numero::with('notes')->find($numeroId);

        broadcast(new NumeroUpdated($numero))->toOthers();

        return redirect()->route('note.manage')->with('success', 'Note deleted.');
    }
}