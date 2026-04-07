<?php

namespace App\Http\Controllers;

use App\Models\Permanence;
use App\Models\Destination;
use App\Models\Groupe;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\PermanenceUpdated;

class PermanenceController extends Controller
{
    public function index(Request $request)
    {
        $permanences = Permanence::with('destinations.numeros')
            ->when($request->search, function ($query) use ($request) {
                $query->where('PSemaine', 'like', '%' . $request->search . '%')
                      ->orWhere('RSemaine', 'like', '%' . $request->search . '%');
            })
            ->when($request->start_date, function ($query) use ($request) {
                $query->where('DSemaine', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                $query->where('FSemaine', '<=', $request->end_date);
            })
            ->orderBy('DSemaine', $request->sort === 'asc' ? 'asc' : 'desc')
            ->paginate(10)
            ->withQueryString();

        $permanences->getCollection()->transform(function ($permanence) {
            $permanence->week_status = $permanence->week_status;
            $permanence->week_status_color = $permanence->week_status_color;
            return $permanence;
        });

        if ($request->week_status) {
            $permanences->setCollection(
                $permanences->getCollection()->filter(fn($p) => $p->week_status === $request->week_status)
            );
        }

        $destinations = Destination::with('numeros.technologie')->get();

        return Inertia::render('Permanences/Index', [
            'permanences' => $permanences,
            'destinations' => $destinations,
            'search' => $request->search,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'sort' => $request->sort ?? 'desc',
            'week_status' => $request->week_status
        ]);
    }

    public function create()
    {
        $group = Groupe::where('groupes', 'permanence')->first();

        $destinations = $group
            ? $group->destinations()->with('organisme')->get()
            : Destination::with('organisme')->get();

        return Inertia::render('Permanences/Create', [
            'destinations' => $destinations
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'DSemaine' => 'required|date',
            'FSemaine' => 'required|date|after_or_equal:DSemaine',
            'PSemaine' => 'required|string|max:255',
            'RSemaine' => 'required|string|max:255',
        ]);

        if (Permanence::where('DSemaine', $data['DSemaine'])->exists()) {
            return back()->withErrors([
                'DSemaine' => "La date ({$data['DSemaine']}) existe."
            ]);
        }

        $permanence = Permanence::create($data);

        // ✅ load relations
        $permanence->load('destinations.numeros.organisme');

        // 🚀 broadcast CREATE
        broadcast(new PermanenceUpdated($permanence, 'create'))->toOthers();

        return redirect()->route('permanences.index')
            ->with('success', 'Permanence created successfully.');
    }

    public function show(Permanence $permanence)
    {
        $permanence->load('destinations.numeros');

        return Inertia::render('Permanences/Show', [
            'permanence' => $permanence
        ]);
    }

    public function edit(Permanence $permanence)
    {
        $permanence->load('destinations');

        $group = Groupe::where('groupes', 'permanence')->first();

        $destinations = $group
            ? $group->destinations()->with('organisme')->get()
            : Destination::with('organisme')->get();

        $selected_destinations = $permanence->destinations->map(fn($d) => [
            'id' => $d->id,
            'ordre' => $d->pivot->ordre
        ])->toArray();

        return Inertia::render('Permanences/Edit', [
            'permanence' => $permanence,
            'destinations' => $destinations,
            'selected_destinations' => $selected_destinations
        ]);
    }

    public function update(Request $request, Permanence $permanence)
    {
        $data = $request->validate([
            'DSemaine' => 'required|date',
            'FSemaine' => 'required|date|after_or_equal:DSemaine',
            'PSemaine' => 'required|string|max:255',
            'RSemaine' => 'required|string|max:255',
        ]);

        $permanence->update($data);

        // ✅ reload relations
        $permanence->load('destinations.numeros.organisme');

        // 🚀 broadcast UPDATE
        broadcast(new PermanenceUpdated($permanence, 'update'))->toOthers();

        return redirect()->route('permanences.show', $permanence)
            ->with('success', 'Permanence updated successfully.');
    }

    public function destroy(Permanence $permanence)
    {
        $id = $permanence->id;

        $permanence->destinations()->detach();
        $permanence->delete();

        // 🚀 broadcast DELETE
        broadcast(new PermanenceUpdated(['id' => $id], 'delete'))->toOthers();

        return redirect()->route('permanences.index')
            ->with('success', 'Permanence deleted successfully.');
    }

    public function deletePrecedant()
    {
        $today = now()->format('Y-m-d');

        $permanences = Permanence::where('FSemaine', '<', $today)->get();

        $count = 0;

        foreach ($permanences as $p) {
            $id = $p->id;

            $p->destinations()->detach();
            $p->delete();

            broadcast(new PermanenceUpdated(['id' => $id], 'delete'))->toOthers();

            $count++;
        }

        return redirect()->route('permanences.index')
            ->with('success', "$count permanence(s) supprimée(s).");
    }

    public function thisWeek()
    {
        $today = now()->format('Y-m-d');

        $permanence = Permanence::where('DSemaine', '<=', $today)
            ->where('FSemaine', '>=', $today)
            ->with('destinations.numeros.organisme')
            ->first();

        $destinations = Destination::with('numeros.technologie')->get();

        return Inertia::render('Permanences/ThisWeek', [
            'permanence' => $permanence,
            'destinations' => $destinations
        ]);
    }

    public function apiThisWeek()
    {
        $today = now()->format('Y-m-d');

        $permanence = Permanence::where('DSemaine', '<=', $today)
            ->where('FSemaine', '>=', $today)
            ->with('destinations.numeros.organisme')
            ->first();

        return response()->json($permanence);
    }

    public function apiProchainPermanences()
    {
        $today = now()->format('Y-m-d');

        $permanences = Permanence::where('DSemaine', '>', $today)
            ->with('destinations.numeros.organisme')
            ->get();

        return response()->json([
            'count' => $permanences->count(),
            'permanences' => $permanences
        ]);
    }
}