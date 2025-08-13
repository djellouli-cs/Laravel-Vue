<?php

namespace App\Http\Controllers;

use App\Models\IpAddress;
use App\Models\Plage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IpAddressController extends Controller
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'ipAdresses' => 'required|ip',
        'organisme' => 'required|string',
        'destination' => 'required|string',
        'Application' => 'required|string',
        'port' => 'nullable|integer',
        'mask' => 'nullable|integer',
        'note' => 'nullable|string',
    ]);

    IpAddress::create($validated);

    return redirect()->back()->with('success', 'Address added successfully!');
}

    public function index()
{
    return Inertia::render('Address/Index', [
        'ipaddresses' => IpAddress::all()
        
    ]);
}
  public function update(Request $request, $id)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'ipAdresses' => 'required|ip',
            'organisme' => 'required|string',
            'destination' => 'required|string',
            'Application' => 'required|string',
            'port' => 'nullable|integer',
            'mask' => 'nullable|integer',
            'note' => 'nullable|string',
        ]);

        // Find the IP address by ID
        $ipAddress = IpAddress::find($id);

        // If not found, return a response or error
        if (!$ipAddress) {
            return response()->json(['error' => 'IP address not found'], 404);
        }

        // Update the IP address with the validated data
        $ipAddress->update($validated);

        // Return a response (optional: success message)
        return redirect()->route('Address.index')->with('success', 'IP address updated successfully!');
    }

public function destroy(IpAddress $ipAddress)
{
    $ipAddress->delete();

    return redirect()->route('Address.index')->with('success', 'IP address deleted successfully.');
}

public function index2()
{
    return Inertia::render('Address/About', [
        'ipaddresses' => IpAddress::with('plage')->get()
        
    ]);
}
public function indexOrganism()
{
    return Inertia::render('Address/AboutOrganisme', [
        'ipaddresses' => IpAddress::all()
        
    ]);
}
public function indexApplication()
{
    return Inertia::render('Address/AboutApplication', [
        'ipaddresses' => IpAddress::all()
        
    ]);
}
public function indexDestination()
{
    return Inertia::render('Address/AboutDestination', [
        'ipaddresses' => IpAddress::all()
        
    ]);
}
}
