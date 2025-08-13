<?php


namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        return Inertia::render('Numero/MultiCrud/ManageService', [
            'services' => Service::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_fr' => 'nullable|string|max:255'
        ]);
        Service::create($request->only(['name', 'name_fr']));

        return redirect()->back()->with('success', 'Service created.');
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_fr' => 'nullable|string|max:255'
        ]);
        $service->update($request->only(['name', 'name_fr']));

        return redirect()->back()->with('success', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->back()->with('success', 'Service deleted.');
    }
}
