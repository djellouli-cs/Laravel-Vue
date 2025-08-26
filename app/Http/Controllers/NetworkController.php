<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NetworkController extends Controller
{
    public function ping(Request $request)
    {
        $ip = $request->query('ip');  // Get the IP from query

        if (!$ip) {
            return response()->json(['reachable' => false], 400); // error if no IP
        }

        // Linux ping: -c 1 = send 1 packet, -W 1 = wait 1 second
        $cmd = escapeshellcmd("ping -c 1 -W 1 $ip");
        exec($cmd, $output, $status);

        // $status === 0 if ping is successful
        return response()->json([
            'reachable' => $status === 0,
        ]);
    }
}
