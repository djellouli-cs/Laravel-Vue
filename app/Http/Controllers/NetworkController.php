<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class NetworkController extends Controller
{
    public function ping(Request $request)
    {
        $ip = $request->query('ip');  // Get the IP address from the query parameter

    if (!$ip) {
        return response()->json(['reachable' => false], 400);  // Return an error if no IP is provided
    }

    // Windows-compatible ping command (-n 1: 1 ping, -w 1000: 1000ms timeout)
    $result = exec("ping -n 1 -w 1000 $ip", $output, $status);

    // If the status is 0, the IP is reachable
    return response()->json([
        'reachable' => $status === 0,
    ]);

    }
}
