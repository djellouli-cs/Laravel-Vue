<?php

namespace App\Http\Controllers;
use App\Models\IpAddress;

use App\Models\Plage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlageController extends Controller
{
    // ✅ Show the update form page
    public function showUpdateRange()
    {
        return Inertia::render('plage/UpdateRange');
        // Make sure the Vue file is in: resources/js/Pages/plage/UpdateRange.vue
    }

    // ✅ Handle the form submission
    public function updateDirectionRange(Request $request)
    {
        $request->validate([
            'ip1' => 'required|ip',
            'ip2' => 'required|ip',
            'direction' => 'required|string|max:255',
        ]);

        $startIp = ip2long($request->ip1);
        $endIp = ip2long($request->ip2);

        // 🛡️ Ensure IPs are in correct order
        if ($startIp > $endIp) {
            [$startIp, $endIp] = [$endIp, $startIp];
        }

        // ✅ Update all IPs within the range
        Plage::whereRaw("INET_ATON(ipAdresses) BETWEEN ? AND ?", [$startIp, $endIp])
            ->update(['direction' => $request->direction]);

        // ✅ Redirect back with success message
        return redirect()->route('plageTable')->with('success', 'Direction updated for IP range.');
    }

    public function plageUse()
{
    return Inertia::render('plage/plage', [
        'ipaddresses' => IpAddress::with('plage')->get()
    ]);
}
 public function plageNoUse()
{
    return Inertia::render('plage/plageNoUse', [
        'ipaddresses' => Plage::with('ipaddresses')->get()
    ]);
}

}
