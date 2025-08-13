<?php

namespace App\Http\Controllers;

use App\Models\Numero;
use App\Models\Destination;
use App\Models\Classe;
use App\Models\Type;
use App\Models\Reserve;
use App\Models\Technologie;
use App\Models\Facture;
use App\Models\Matricule;
use App\Models\Organisme;
use App\Models\Acheminement;
use App\Models\Service;

use Illuminate\Http\Request;
use Inertia\Inertia;

class NumeroController extends Controller
{
    /**
     * Affiche la page de gestion des numéros (CRUD Vue.js via Inertia).
     */
    public function manageNumero()
    {
        return Inertia::render('Numero/MultiCrud/ManagerNumero', [
            'numeros' => Numero::with([
                'destination',
                'classe',
                'type',
                'reserve',
                'technologie',
                'facture',
                'matricule',
                'organisme',
                'service',
                'acheminements',
                'fax',
                'user'
            ])->get(),

            // Envoyer les options de sélection
            'destinations' => Destination::all(),
            'classes' => Classe::all(),
            'types' => Type::all(),
            'reserves' => Reserve::all(),
            'technologies' => Technologie::all(),
            'factures' => Facture::all(),
            'matricules' => Matricule::all(),
            'organismes' => Organisme::all(),
            'services' => Service::all(),
            'acheminements' => Acheminement::all(),
        ]);
    }

    /**
     * Enregistre un nouveau numéro.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate($this->validationRules(), $this->messages());

            // Handle null values explicitly
            $formData = $request->all();

            // Ensure nullable fields are set to null if they are empty
            foreach ($formData as $key => $value) {
                if (empty($value)) {
                    $formData[$key] = null;
                }
            }

            // Create the Numero with user_id
            $formData['user_id'] = auth()->id();
            Numero::create($formData);

            return redirect()->route('numero.manage')->with('success', 'Numéro créé avec succès.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    /**
     * Met à jour un numéro existant.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate($this->validationRules($id), $this->messages());

            // Find the Numero by ID
            $numero = Numero::findOrFail($id);

            // Handle null values explicitly
            $formData = $request->all();
            
            // Ensure nullable fields are set to null if they are empty
            foreach ($formData as $key => $value) {
                if (empty($value)) {
                    $formData[$key] = null;
                }
            }

            // Update the Numero with user_id
            $formData['user_id'] = auth()->id();
            $numero->update($formData);

            return redirect()->route('numero.manage')->with('success', 'Numéro mis à jour avec succès.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    /**
     * Supprime un numéro.
     */
    public function destroy($id)
    {
        $numero = Numero::findOrFail($id);
        $numero->delete();

        return redirect()->route('numero.manage')->with('success', 'Numéro supprimé avec succès.');
    }

    /**
     * Règles de validation communes à store() et update().
     */
    private function validationRules($id = null): array
    {
        $uniqueRule = 'unique:numeros,NDappel';
        if ($id) {
            $uniqueRule .= ',' . $id;
        }
        return [
            'NDappel' => ['required', 'string', 'max:255', $uniqueRule],
            'Position' => 'required|string|max:255',
            'destination_id' => 'nullable|exists:destinations,id',
            'classe_id' => 'nullable|exists:classes,id',
            'type_id' => 'nullable|exists:types,id',
            'reserve_id' => 'nullable|exists:reserves,id',
            'technologie_id' => 'nullable|exists:technologies,id',
            'facture_id' => 'nullable|exists:factures,id',
            'matricule_id' => 'nullable|exists:matricules,id',
            'organisme_id' => 'nullable|exists:organismes,id',
            'service_id' => 'nullable|exists:services,id',
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages()
    {
        return [
            'NDappel.unique' => 'Ce numéro d\'appel existe déjà.',
            'NDappel.required' => 'Le numéro d\'appel est requis.',
            'Position.required' => 'La position est requise.',
        ];
    }
    public function edit($id)
{
    $numero = Numero::with([
        'destination',
        'classe',
        'type',
        'reserve',
        'technologie',
        'facture',
        'matricule',
        'organisme',
        'service',
        'acheminements'
    ])->findOrFail($id);

    return Inertia::render('Numero/MultiCrud/EditNumero', [
        'numero' => $numero,
        'destinations' => Destination::all(),
        'classes' => Classe::all(),
        'types' => Type::all(),
        'reserves' => Reserve::all(),
        'technologies' => Technologie::all(),
        'factures' => Facture::all(),
        'matricules' => Matricule::all(),
        'organismes' => Organisme::all(),
        'services' => Service::all(),
        'acheminements' => Acheminement::all(),
    ]);
}

}
