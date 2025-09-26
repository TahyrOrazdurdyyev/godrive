<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Driver;

class DriverMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if (!$user || !($user instanceof Driver)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Driver authentication required.'
            ], 403);
        }

        return $next($request);
    }
}

