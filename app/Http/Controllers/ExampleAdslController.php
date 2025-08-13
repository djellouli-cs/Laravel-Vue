<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Numero;
use Illuminate\Http\Request;

class ExampleAdslController extends Controller
{
    /**
     * Create a new ADSL facture
     */
    public function createAdslFacture(Request $request)
    {
        $request->validate([
            'ndappel' => 'required|string|max:10',
            'adsl_provider' => 'required|string',
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

        return response()->json([
            'message' => 'ADSL facture created successfully',
            'facture' => $facture,
            'total_cost' => $facture->getAdslTotalCost(),
        ]);
    }

    /**
     * Get all ADSL factures
     */
    public function getAdslFactures()
    {
        $adslFactures = Facture::adsl()
            ->with(['numeros', 'user'])
            ->get();

        return response()->json([
            'adsl_factures' => $adslFactures,
            'count' => $adslFactures->count(),
        ]);
    }

    /**
     * Get ADSL factures by provider
     */
    public function getAdslFacturesByProvider($provider)
    {
        $factures = Facture::adsl()
            ->where('adsl_provider', $provider)
            ->with(['numeros', 'user'])
            ->get();

        return response()->json([
            'provider' => $provider,
            'factures' => $factures,
            'total_monthly_cost' => $factures->sum('adsl_monthly_cost'),
        ]);
    }

    /**
     * Update ADSL facture
     */
    public function updateAdslFacture(Request $request, $id)
    {
        $facture = Facture::findOrFail($id);
        
        if (!$facture->is_adsl) {
            return response()->json(['error' => 'This is not an ADSL facture'], 400);
        }

        $request->validate([
            'adsl_provider' => 'sometimes|string',
            'adsl_plan' => 'sometimes|string',
            'adsl_monthly_cost' => 'sometimes|numeric|min:0',
            'adsl_start_date' => 'sometimes|date',
            'adsl_end_date' => 'sometimes|date|after:adsl_start_date',
            'adsl_notes' => 'sometimes|string',
        ]);

        $facture->update($request->only([
            'adsl_provider',
            'adsl_plan',
            'adsl_monthly_cost',
            'adsl_start_date',
            'adsl_end_date',
            'adsl_notes',
        ]));

        return response()->json([
            'message' => 'ADSL facture updated successfully',
            'facture' => $facture->fresh(),
            'total_cost' => $facture->getAdslTotalCost(),
        ]);
    }

    /**
     * Get statistics for ADSL factures
     */
    public function getAdslStatistics()
    {
        $adslFactures = Facture::adsl();
        
        $stats = [
            'total_adsl_factures' => $adslFactures->count(),
            'total_monthly_cost' => $adslFactures->sum('adsl_monthly_cost'),
            'providers' => $adslFactures->distinct('adsl_provider')->pluck('adsl_provider'),
            'average_monthly_cost' => $adslFactures->avg('adsl_monthly_cost'),
        ];

        return response()->json($stats);
    }
} 