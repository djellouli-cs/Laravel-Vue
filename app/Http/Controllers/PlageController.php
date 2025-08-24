<?php

namespace App\Http\Controllers;

use App\Models\IpAddress;
use App\Models\Plage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlageController extends Controller
{
    // Show the update form page
    public function showUpdateRange()
    {
        return Inertia::render('plage/UpdateRange');
    }

    // Handle the form submission to update direction
    public function updateDirectionRange(Request $request)
    {
        $request->validate([
            'ip1' => 'required|ip',
            'ip2' => 'required|ip',
            'direction' => 'required|string|max:255',
        ]);

        $startIp = ip2long($request->ip1);
        $endIp = ip2long($request->ip2);

        // Ensure IPs are in correct order
        if ($startIp > $endIp) {
            [$startIp, $endIp] = [$endIp, $startIp];
        }

        // Update all IPs within the range
        Plage::whereRaw("INET_ATON(ipAdresses) BETWEEN ? AND ?", [$startIp, $endIp])
            ->update(['direction' => $request->direction]);

        return redirect()->route('plageTable')
            ->with('success', 'Direction updated for IP range.');
    }

    // Show IP addresses with plage relationship
    public function plageUse()
    {
        return Inertia::render('plage/plage', [
            'ipaddresses' => IpAddress::with('plage')->get()
        ]);
    }

    // Show Plages with their IP addresses
    public function plageNoUse()
    {
        return Inertia::render('plage/plageNoUse', [
            'ipaddresses' => Plage::with('ipaddresses')->get()
        ]);
    }
}
