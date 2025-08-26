<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NetworkController extends Controller
{
    public function ping(Request $request)
    {
        $ip = $request->query('ip');  // Get the IP address from the query parameter

        if (!$ip) {
            return response()->json(['reachable' => false], 400);  // Return error if no IP
        }

        // Linux-compatible ping command (-c 1: 1 ping, -W 1: 1 second timeout)
        $result = exec("ping -c 1 -W 1 $ip", $output, $status);

        // Return whether the IP is reachable
        return response()->json([
            'reachable' => $status === 0,
        ]);
    }
}
