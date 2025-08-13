<?php

namespace App\Http\Controllers;

use App\Models\Permanence;
use App\Models\Destination;
use App\Models\Groupe;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

        // Add week status to each permanence
        $permanences->getCollection()->transform(function ($permanence) {
            $permanence->week_status = $permanence->week_status;
            $permanence->week_status_color = $permanence->week_status_color;
            return $permanence;
        });

        // Filter by week status if requested
        if ($request->week_status) {
            $permanences->setCollection(
                $permanences->getCollection()->filter(function ($permanence) use ($request) {
                    return $permanence->week_status === $request->week_status;
                })
            );
        }

        // Get all destinations with their numeros for the helper functions
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
        // Get destinations that belong to the "permanence" group
        $permanenceGroup = Groupe::where('groupes', 'permanence')->first();
        
        if ($permanenceGroup) {
            $destinations = $permanenceGroup->destinations()->with('organisme')->get();
        } else {
            // Fallback to all destinations if no permanence group exists
            $destinations = Destination::with('organisme')->get();
        }
        
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

        // Check if date already exists
        $existingPermanence = Permanence::where('DSemaine', $data['DSemaine'])->first();
        if ($existingPermanence) {
            return back()->withErrors(['DSemaine' => "La date ({$data['DSemaine']}) existe."]);
        }

        $permanence = Permanence::create($request->only([
            'DSemaine', 'FSemaine', 'PSemaine', 'RSemaine'
        ]));

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
        
        // Get destinations that belong to the "permanence" group
        $permanenceGroup = Groupe::where('groupes', 'permanence')->first();
        
        if ($permanenceGroup) {
            $destinations = $permanenceGroup->destinations()->with('organisme')->get();
        } else {
            // Fallback to all destinations if no permanence group exists
            $destinations = Destination::with('organisme')->get();
        }
        
        // Prepare selected destinations for the form
        $selected_destinations = $permanence->destinations->map(function ($destination) {
            return [
                'id' => $destination->id,
                'ordre' => $destination->pivot->ordre
            ];
        })->toArray();

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

        $permanence->update($request->only([
            'DSemaine', 'FSemaine', 'PSemaine', 'RSemaine'
        ]));

        return redirect()->route('permanences.show', $permanence)
            ->with('success', 'Permanence updated successfully.');
    }

    public function destroy(Permanence $permanence)
    {
        $permanence->destinations()->detach();
        $permanence->delete();

        return redirect()->route('permanences.index')
            ->with('success', 'Permanence deleted successfully.');
    }

    public function deletePrecedant()
    {
        $today = now();
        
        // Get all permanences that are PRECEDANT (past permanences)
        $precedantPermanences = Permanence::where('FSemaine', '<', $today->format('Y-m-d'))->get();
        
        $deletedCount = 0;
        
        foreach ($precedantPermanences as $permanence) {
            // Detach destinations first
            $permanence->destinations()->detach();
            // Delete the permanence
            $permanence->delete();
            $deletedCount++;
        }
        
        return redirect()->route('permanences.index')
            ->with('success', "{$deletedCount} permanence(s) PRECEDANT supprimée(s) avec succès.");
    }

    public function thisWeek()
    {
        $today = now()->format('Y-m-d');
        \Log::info('Today is: ' . $today);

        // Find permanence for today
        $thisWeekPermanence = Permanence::where('DSemaine', '<=', $today)
            ->where('FSemaine', '>=', $today)
            ->with('destinations.numeros.organisme')
            ->first();

        \Log::info('Found permanence:', $thisWeekPermanence ? $thisWeekPermanence->toArray() : []);

        // Get all destinations with their numeros for the helper functions (like in index)
        $destinations = \App\Models\Destination::with('numeros.technologie')->get();

        return Inertia::render('Permanences/ThisWeek', [
            'permanence' => $thisWeekPermanence,
            'currentWeek' => [
                'start' => $today,
                'end' => $today,
                'startFormatted' => now()->format('d/m/Y'),
                'endFormatted' => now()->format('d/m/Y')
            ],
            'destinations' => $destinations
        ]);
    }

    public function apiThisWeek()
    {
        $today = now()->format('Y-m-d');
        \Log::info('Today is: ' . $today);

        $permanence = Permanence::where('DSemaine', '<=', $today)
            ->where('FSemaine', '>=', $today)
            ->with('destinations.numeros.organisme')
            ->first();

        \Log::info('Found permanence:', $permanence ? $permanence->toArray() : []);

        return response()->json($permanence);
    }

    public function apiProchainPermanences()
    {
        $today = now();
        
        $prochainPermanences = Permanence::where('DSemaine', '>', $today->format('Y-m-d'))
            ->with('destinations.numeros.organisme')
            ->get();

        return response()->json([
            'count' => $prochainPermanences->count(),
            'permanences' => $prochainPermanences
        ]);
    }
}
