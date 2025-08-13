<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FactureController extends Controller
{
    public function manageFacture()
    {
        return Inertia::render('Numero/MultiCrud/ManagerFacture', [
            'factures' => Facture::all(),
        ]);
    }

    public function index()
    {
        return Facture::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'is_factured' => 'required|boolean',
            'facture' => ['nullable', 'string', 'max:255'],
            'provider' => 'nullable|string|in:Algerie Telecom,Mobilis',
            'plan' => 'nullable|string',
            'monthly_cost' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'notes' => 'nullable|string',
        ]);

        if ($request->is_factured) {
            Facture::create([
                'facture' => $request->facture, // can be null
                'is_factured' => true,
                'provider' => $request->provider,
                'plan' => $request->plan,
                'monthly_cost' => $request->monthly_cost,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'notes' => $request->notes,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('facture.manage')->with('success', 'Facture créée.');
        }

        return redirect()->route('facture.manage')->with('success', 'Non sauvegardé. is_factured était false.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'is_factured' => 'required|boolean',
            'facture' => ['nullable', 'string', 'max:255'],
            'provider' => 'nullable|string|in:Algerie Telecom,Mobilis',
            'plan' => 'nullable|string',
            'monthly_cost' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'notes' => 'nullable|string',
        ]);

        $facture = Facture::findOrFail($id);
        $facture->facture = $request->is_factured ? $request->facture : null;
        $facture->is_factured = $request->is_factured;
        $facture->provider = $request->provider;
        $facture->plan = $request->plan;
        $facture->monthly_cost = $request->monthly_cost;
        $facture->start_date = $request->start_date;
        $facture->end_date = $request->end_date;
        $facture->notes = $request->notes;
        $facture->save();

        return redirect()->route('facture.manage')->with('success', 'Facture mise à jour.');
    }

    public function destroy($id)
    {
        $facture = Facture::findOrFail($id);
        $facture->delete();

        return redirect()->route('facture.manage')->with('success', 'Facture supprimée.');
    }
}
