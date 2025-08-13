<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Numero;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdslController extends Controller
{
    /**
     * Display the ADSL management page
     */
    public function index()
    {
        $adslFactures = Facture::adsl()
            ->with(['numeros', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $statistics = [
            'total_adsl_factures' => Facture::adsl()->count(),
            'total_monthly_cost' => Facture::adsl()->sum('adsl_monthly_cost'),
            'providers' => Facture::adsl()->distinct('adsl_provider')->pluck('adsl_provider'),
            'average_monthly_cost' => Facture::adsl()->avg('adsl_monthly_cost'),
        ];

        return Inertia::render('Adsl/AdslManagement', [
            'adslFactures' => $adslFactures,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Create a new ADSL facture
     */
    public function store(Request $request)
    {
        $request->validate([
            'ndappel' => 'required|string|max:10',
            'adsl_provider' => 'required|string|in:Algerie Telecom,Mobilis',
            'adsl_plan' => 'required|string',
            'adsl_monthly_cost' => 'required|numeric|min:0',
            'adsl_start_date' => 'required|date',
            'adsl_end_date' => 'nullable|date|after:adsl_start_date',
            'adsl_notes' => 'nullable|string',
        ]);

        // Create the ADSL facture
        $facture = Facture::create([
            'facture' => 'ADSL-' . $request->ndappel,
            'is_factured' => false,
            'is_adsl' => true,
            'adsl_provider' => $request->adsl_provider,
            'adsl_plan' => $request->adsl_plan,
            'adsl_monthly_cost' => $request->adsl_monthly_cost,
            'adsl_start_date' => $request->adsl_start_date,
            'adsl_end_date' => $request->adsl_end_date,
            'adsl_notes' => $request->adsl_notes,
            'user_id' => auth()->id(),
        ]);

        // Link the numero to this facture
        $numero = Numero::where('NDappel', $request->ndappel)->first();
        if ($numero) {
            $numero->update(['facture_id' => $facture->id]);
        }

        return redirect()->back()->with('success', 'Service ADSL créé avec succès.');
    }

    /**
     * Update ADSL facture
     */
    public function update(Request $request, $id)
    {
        $facture = Facture::findOrFail($id);
        
        if (!$facture->is_adsl) {
            return redirect()->back()->with('error', 'This is not an ADSL facture.');
        }

        $request->validate([
            'ndappel' => 'required|string|max:10',
            'adsl_provider' => 'required|string|in:Algerie Telecom,Mobilis',
            'adsl_plan' => 'required|string',
            'adsl_monthly_cost' => 'required|numeric|min:0',
            'adsl_start_date' => 'required|date',
            'adsl_end_date' => 'nullable|date|after:adsl_start_date',
            'adsl_notes' => 'nullable|string',
        ]);

        $facture->update([
            'adsl_provider' => $request->adsl_provider,
            'adsl_plan' => $request->adsl_plan,
            'adsl_monthly_cost' => $request->adsl_monthly_cost,
            'adsl_start_date' => $request->adsl_start_date,
            'adsl_end_date' => $request->adsl_end_date,
            'adsl_notes' => $request->adsl_notes,
        ]);

        // Update the linked numero if NDappel changed
        $numero = Numero::where('NDappel', $request->ndappel)->first();
        if ($numero) {
            $numero->update(['facture_id' => $facture->id]);
        }

        return redirect()->back()->with('success', 'Service ADSL mis à jour avec succès.');
    }

    /**
     * Delete ADSL facture
     */
    public function destroy($id)
    {
        $facture = Facture::findOrFail($id);
        
        if (!$facture->is_adsl) {
            return redirect()->back()->with('error', 'This is not an ADSL facture.');
        }

        // Remove the link from numeros
        Numero::where('facture_id', $facture->id)->update(['facture_id' => null]);
        
        $facture->delete();

        return redirect()->back()->with('success', 'Service ADSL supprimé avec succès.');
    }

    /**
     * Get ADSL factures by provider
     */
    public function byProvider($provider)
    {
        $factures = Facture::adsl()
            ->where('adsl_provider', $provider)
            ->with(['numeros', 'user'])
            ->get();

        $statistics = [
            'provider' => $provider,
            'total_factures' => $factures->count(),
            'total_monthly_cost' => $factures->sum('adsl_monthly_cost'),
            'average_monthly_cost' => $factures->avg('adsl_monthly_cost'),
        ];

        return Inertia::render('Adsl/AdslByProvider', [
            'factures' => $factures,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get ADSL statistics
     */
    public function statistics()
    {
        $adslFactures = Facture::adsl();
        
        $statistics = [
            'total_adsl_factures' => $adslFactures->count(),
            'total_monthly_cost' => $adslFactures->sum('adsl_monthly_cost'),
            'providers' => $adslFactures->distinct('adsl_provider')->pluck('adsl_provider'),
            'average_monthly_cost' => $adslFactures->avg('adsl_monthly_cost'),
            'providers_breakdown' => $adslFactures->selectRaw('adsl_provider, COUNT(*) as count, SUM(adsl_monthly_cost) as total_cost')
                ->groupBy('adsl_provider')
                ->get(),
        ];

        return Inertia::render('Adsl/AdslStatistics', [
            'statistics' => $statistics,
        ]);
    }
} 